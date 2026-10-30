<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\FilesModel;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('velo_gallery', category: 'velo', template: 'content_element/velo_gallery')]
class VeloGalleryController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $rows = StringUtil::deserialize($model->velo_gallery_images, true);
        $images = [];

        foreach ($rows as $row) {
            if (empty($row['src'])) {
                continue;
            }

            $file = FilesModel::findByUuid($row['src']);

            if (null === $file) {
                continue;
            }

            $images[] = [
                'src' => '/' . $file->path,
                'alt' => $row['alt'] ?: '',
            ];
        }

        $template->set('images', $images);

        return $template->getResponse();
    }
}
