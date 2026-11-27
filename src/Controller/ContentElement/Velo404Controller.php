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
 * The content of the error_404 page (see tl_page.id=12, "Fehlerseite 404") -- same header/footer
 * as every other page (that page has no own layout override, so it inherits the root's), just
 * this one section in its article instead of regular content. Fully static -- no backend fields
 * at all, the copy is fixed on purpose (see the template).
 */
#[AsContentElement('velo_404', category: 'velo', template: 'content_element/velo_404')]
class Velo404Controller extends AbstractContentElementController
{
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        return $template->getResponse();
    }
}
