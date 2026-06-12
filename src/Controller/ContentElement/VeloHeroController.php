<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FilesModel;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('velo_hero', category: 'velo', template: 'content_element/velo_hero')]
class VeloHeroController extends AbstractContentElementController
{
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

        $imgSrc = null;
        $imgAlt = '';
        if ($model->addImage && $model->singleSRC) {
            $file = FilesModel::findByUuid($model->singleSRC);
            if ($file !== null) {
                $imgSrc = '/' . $file->path;
                $imgAlt = $model->alt ?: '';
            }
        }
        $template->set('img_src', $imgSrc);
        $template->set('img_alt', $imgAlt);

        return $template->getResponse();
    }
}
