<?php

declare(strict_types=1);

namespace App\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Prototyped in /tmp as "Variante A4" (velo-unterstuetzer-anmeldung-hell-v2.html). Frontend-only
 * for now: the template's inline <script> validates the e-mail field and the Sicherheitsfrage
 * and shows a success state on the client, but there is no backend/API call yet -- nothing is
 * actually submitted or persisted.
 */
#[AsContentElement('velo_signup', category: 'velo', template: 'content_element/velo_signup')]
class VeloSignupController extends AbstractContentElementController
{
    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services[ContentUrlGenerator::class] = ContentUrlGenerator::class;

        return $services;
    }

    /**
     * Same helper as VeloHeroController::resolvePageHref() -- returns null if no page is
     * selected or the page can no longer be routed, so the template can hide the Firma-Link
     * instead of linking nowhere.
     */
    private function resolvePageHref(int $pageId): ?string
    {
        if ($pageId <= 0) {
            return null;
        }

        $page = PageModel::findById($pageId);

        if ($page === null) {
            return null;
        }

        try {
            return $this->container->get(ContentUrlGenerator::class)->generate($page);
        } catch (ExceptionInterface) {
            return null;
        }
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('section_eyebrow', $model->velo_section_eyebrow);
        $template->set('section_heading', $model->velo_section_heading);
        $template->set('section_lead', $model->velo_section_lead);
        $template->set('btn_primary_label', $model->velo_btn_primary_label ?: 'Unterstützer:in werden');
        $template->set('btn_secondary_label', $model->velo_btn_secondary_label ?: 'Als Firma oder Organisation anmelden');
        $template->set('btn_secondary_href', $this->resolvePageHref((int) $model->velo_btn_secondary_page));

        return $template->getResponse();
    }
}
