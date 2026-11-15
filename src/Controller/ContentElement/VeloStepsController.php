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

#[AsContentElement('velo_steps', category: 'velo', template: 'content_element/velo_steps')]
class VeloStepsController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('section_eyebrow', $model->velo_section_eyebrow);
        $template->set('section_heading', $model->velo_section_heading);

        $rawSteps = StringUtil::deserialize($model->velo_steps, true);
        $steps = [];
        foreach ($rawSteps as $i => $step) {
            if (empty($step['title']) && empty($step['body'])) {
                continue;
            }

            $step['number'] = sprintf('%02d', $i + 1);
            $steps[] = $step;
        }
        $template->set('steps', $steps);

        return $template->getResponse();
    }
}
