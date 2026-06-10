<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('velo_melde', category: 'velo', template: 'content_element/velo_melde')]
class VeloMeldeController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('eyebrow', $model->velo_eyebrow);
        $template->set('text', $model->text);
        $template->set('btn_label', $model->velo_btn_label ?: 'Stelle melden');
        $template->set('form_id', $model->velo_form_id);

        return $template->getResponse();
    }
}
