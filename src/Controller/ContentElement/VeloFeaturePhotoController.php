<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\FilesModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('velo_feature_photo', category: 'velo', template: 'content_element/velo_feature_photo')]
class VeloFeaturePhotoController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('eyebrow', $model->velo_eyebrow);
        $template->set('text', $model->velo_fp_text);

        $imgSrc = null;

        if ($model->velo_fp_image) {
            $file = FilesModel::findByUuid($model->velo_fp_image);

            if (null !== $file) {
                $imgSrc = '/' . $file->path;
            }
        }

        $template->set('img_src', $imgSrc);
        $template->set('img_alt', $model->velo_fp_alt);

        return $template->getResponse();
    }
}
