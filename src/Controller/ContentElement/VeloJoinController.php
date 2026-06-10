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

#[AsContentElement('velo_join', category: 'velo', template: 'content_element/velo_join')]
class VeloJoinController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('eyebrow', $model->velo_eyebrow);
        $template->set('btn_primary_label', $model->velo_btn_primary_label);
        $template->set('btn_primary_href', $model->velo_btn_primary_href);
        $template->set('btn_secondary_label', $model->velo_btn_secondary_label);
        $template->set('btn_secondary_href', $model->velo_btn_secondary_href);

        $rawBenefits = StringUtil::deserialize($model->velo_benefits, true);
        $benefits = array_filter(array_column($rawBenefits, 'benefit'));
        $template->set('benefits', array_values($benefits));

        return $template->getResponse();
    }
}
