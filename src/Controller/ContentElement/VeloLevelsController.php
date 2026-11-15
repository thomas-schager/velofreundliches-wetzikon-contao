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

#[AsContentElement('velo_levels', category: 'velo', template: 'content_element/velo_levels')]
class VeloLevelsController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('section_eyebrow', $model->velo_section_eyebrow);
        $template->set('section_heading', $model->velo_section_heading);
        $template->set('section_lead', $model->velo_section_lead);

        $rawLevels = StringUtil::deserialize($model->velo_levels, true);
        $levels = [];
        foreach ($rawLevels as $level) {
            if (empty($level['title'])) {
                continue;
            }

            $level['items'] = array_values(array_filter(array_map(
                'trim',
                explode("\n", (string) ($level['items'] ?? '')),
            )));

            $levels[] = $level;
        }
        $template->set('levels', $levels);

        return $template->getResponse();
    }
}
