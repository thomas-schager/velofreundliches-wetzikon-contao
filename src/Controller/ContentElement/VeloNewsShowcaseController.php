<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FilesModel;
use Contao\Image\PictureConfiguration;
use Contao\Image\PictureConfigurationItem;
use Contao\Image\ResizeConfiguration;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * A larger, richer single-article preview than the regular .vw-news__card grid (see
 * templates/news_latest.html.twig / news_short.html.twig): the full, untruncated teaser
 * (headers/images an editor placed in the TinyMCE field render as-is) instead of a one-sentence
 * excerpt, image bleeding flush on one side instead of a capped 16:9 strip on top.
 *
 * velo_news_mode picks between the latest published item across every news archive (default)
 * or one specific item hand-picked in the backend (velo_news_item) -- see findLatest() for why
 * archives are resolved live rather than a hardcoded ID list. A manual pick that isn't
 * currently publicly viewable (unpublished, before its "anzeigen ab", after its "anzeigen bis")
 * falls back to the latest published item instead -- otherwise "Weiterlesen" would link to a
 * page that 404s for every visitor. The backend's velo_news_item dropdown (see tl_content.php)
 * spells out exactly that status per item, so this fallback is never a silent surprise.
 */
#[AsContentElement('velo_news_showcase', category: 'velo', template: 'content_element/velo_news_showcase')]
class VeloNewsShowcaseController extends AbstractContentElementController
{
    /**
     * Target size for the showcase image, run through Contao's resize/compression pipeline
     * instead of serving the uploaded original as-is -- same AVIF/WebP approach as
     * VeloHeroController::heroPictureConfig(), but "box" instead of "crop": the template deliberately
     * keeps the image at its own natural aspect ratio (no forced 16:9 like the .vw-news__card
     * grid), so resizing must only cap the dimensions, never crop them. 700px covers the
     * element's own ~25%-of-content-width display box at roughly retina density on desktop,
     * where the display box is widest.
     */
    private function showcasePictureConfig(): PictureConfiguration
    {
        return (new PictureConfiguration())
            ->setSize(
                (new PictureConfigurationItem())->setResizeConfig(
                    (new ResizeConfiguration())
                        ->setWidth(700)
                        ->setHeight(700)
                        ->setMode(ResizeConfiguration::MODE_BOX)
                )
            )
            ->setFormats([
                PictureConfiguration::FORMAT_DEFAULT => ['avif', 'webp', PictureConfiguration::FORMAT_DEFAULT],
            ])
        ;
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services[Studio::class] = Studio::class;
        $services[ContentUrlGenerator::class] = ContentUrlGenerator::class;

        return $services;
    }

    private function isCurrentlyViewable(NewsModel $news): bool
    {
        if (!$news->published) {
            return false;
        }

        $now = time();

        if ($news->start && (int) $news->start > $now) {
            return false;
        }

        if ($news->stop && (int) $news->stop < $now) {
            return false;
        }

        return true;
    }

    /**
     * Archive IDs are resolved live (NewsArchiveModel::findAll()) rather than hardcoded -- a
     * future 4th news category is picked up automatically, no change needed here.
     */
    private function findLatest(): NewsModel|null
    {
        $archives = NewsArchiveModel::findAll();

        if ($archives === null) {
            return null;
        }

        $ids = array_map(static fn (NewsArchiveModel $a): int => (int) $a->id, $archives->getModels());

        if ($ids === []) {
            return null;
        }

        // findPublishedByPids() always returns a Collection (or null), even with limit=1.
        $collection = NewsModel::findPublishedByPids($ids, null, 1, 0, ['order' => 'date DESC, time DESC']);

        return $collection?->current();
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $news = null;

        if ('manual' === $model->velo_news_mode && $model->velo_news_item) {
            $candidate = NewsModel::findById((int) $model->velo_news_item);

            if ($candidate !== null && $this->isCurrentlyViewable($candidate)) {
                $news = $candidate;
            }
        }

        // No mode=manual pick, the picked item was deleted, or it isn't currently viewable
        // (unpublished / before "anzeigen ab" / after "anzeigen bis") -- fall back to the
        // latest published item across all categories instead of linking to a 404.
        if ($news === null) {
            $news = $this->findLatest();
        }

        if ($news === null) {
            return $template->getResponse();
        }

        $archive = $news->getRelated('pid');

        $template->set('headline', $news->headline);
        $template->set('teaser', $news->teaser);
        $template->set('date', date('d.m.Y', (int) $news->date));
        $template->set('datetime', date('c', (int) $news->date));
        $template->set('archive_title', $archive?->title);

        try {
            $template->set('link', $this->container->get(ContentUrlGenerator::class)->generate($news));
        } catch (ExceptionInterface) {
            $template->set('link', null);
        }

        $img = null;
        if ($news->addImage && $news->singleSRC) {
            $file = FilesModel::findByUuid($news->singleSRC);

            if ($file !== null) {
                $figure = $this->container->get(Studio::class)->createFigureBuilder()
                    ->fromFilesModel($file)
                    ->setSize($this->showcasePictureConfig())
                    ->buildIfResourceExists()
                ;

                if ($figure !== null) {
                    $img = $figure->getImage()->getImg();
                    $imgSources = $figure->getImage()->getSources();
                }
            }
        }
        $template->set('img', $img);
        $template->set('img_sources', $imgSources ?? []);
        $template->set('img_alt', $news->alt ?: '');

        return $template->getResponse();
    }
}
