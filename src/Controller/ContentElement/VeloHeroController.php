<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FilesModel;
use Contao\Image\PictureConfiguration;
use Contao\Image\PictureConfigurationItem;
use Contao\Image\ResizeConfiguration;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('velo_hero', category: 'velo', template: 'content_element/velo_hero')]
class VeloHeroController extends AbstractContentElementController
{
    /**
     * Target size for the hero image, run through Contao's resize/compression pipeline instead of
     * serving the uploaded original as-is. Matches the display box in site.css (.vw-hero__img,
     * ~560x460 at 1x on desktop) at roughly retina density; "crop" avoids shipping extra pixels the
     * CSS object-fit: cover would just discard anyway.
     *
     * AVIF/WebP are offered as <source> alternatives ahead of the JPEG fallback -- relies on the
     * server's GD/Imagick build being able to encode both; confirmed locally, not yet verified on
     * the Hostpoint production target (see DEPLOY.md once that's written).
     */
    private function heroPictureConfig(): PictureConfiguration
    {
        return (new PictureConfiguration())
            ->setSize(
                (new PictureConfigurationItem())->setResizeConfig(
                    (new ResizeConfiguration())
                        ->setWidth(1200)
                        ->setHeight(960)
                        ->setMode(ResizeConfiguration::MODE_CROP)
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

        return $services;
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('eyebrow', $model->velo_eyebrow);
        $template->set('hero_title', $model->velo_hero_title);
        $template->set('lead', $model->velo_lead);
        $template->set('btn_primary_label', $model->velo_btn_primary_label);
        $template->set('btn_primary_href', $model->velo_btn_primary_href);
        $template->set('btn_secondary_label', $model->velo_btn_secondary_label);
        $template->set('btn_secondary_href', $model->velo_btn_secondary_href);

        $rawStats = StringUtil::deserialize($model->velo_stats, true);
        $stats = [];
        foreach ($rawStats as $s) {
            if (!empty($s['value']) || !empty($s['label'])) {
                $stats[] = $s;
            }
        }
        $template->set('stats', $stats);

        $img = null;
        $imgSources = [];
        $imgAlt = '';
        if ($model->addImage && $model->singleSRC) {
            $file = FilesModel::findByUuid($model->singleSRC);
            if ($file !== null) {
                $figure = $this->container->get(Studio::class)->createFigureBuilder()
                    ->fromFilesModel($file)
                    ->setSize($this->heroPictureConfig())
                    ->buildIfResourceExists();

                if ($figure !== null) {
                    $img = $figure->getImage()->getImg();
                    $imgSources = $figure->getImage()->getSources();
                    $imgAlt = $model->alt ?: '';
                }
            }
        }
        $template->set('img', $img);
        $template->set('img_sources', $imgSources);
        $template->set('img_alt', $imgAlt);

        return $template->getResponse();
    }
}
