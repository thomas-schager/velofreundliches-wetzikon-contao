<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fully fixed trial element -- no editable fields at all. Headline, text and the form to use
 * are hardcoded directly in the template rather than read from the model.
 */
#[AsContentElement('velo_newsletter_compact', category: 'velo', template: 'content_element/velo_newsletter_compact')]
class VeloNewsletterCompactController extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        return $template->getResponse();
    }
}
