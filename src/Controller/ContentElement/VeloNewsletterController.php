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

#[AsContentElement('velo_newsletter', category: 'velo', template: 'content_element/velo_newsletter')]
class VeloNewsletterController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('eyebrow', $model->velo_eyebrow);
        $template->set('text', $model->text);

        $rawItems = StringUtil::deserialize($model->velo_benefits, true);
        $template->set('topics', array_values(array_filter(array_column($rawItems, 'benefit'))));
        $template->set('form_id', $model->velo_form_id);

        return $template->getResponse();
    }
}
