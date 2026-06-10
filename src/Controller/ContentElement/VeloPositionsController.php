<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('velo_positions', category: 'velo', template: 'content_element/velo_positions')]
class VeloPositionsController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('section_eyebrow', $model->velo_section_eyebrow);
        $template->set('section_heading', $model->velo_section_heading);
        $template->set('section_lead', $model->velo_section_lead);

        $rawCards = StringUtil::deserialize($model->velo_cards, true);
        $template->set('cards', $rawCards);

        return $template->getResponse();
    }
}
