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
    /**
     * Maps the stable option key stored in velo_melde_card (see contao/dca/tl_content.php) to
     * the actual path on the VeloMelder backend app. New Meldekarten only need a new DCA option
     * + a new entry here, nothing else.
     */
    private const MELDE_CARD_PATHS = [
        'gelbes-band' => '/velomelder-gelbes-band.html',
    ];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('eyebrow', $model->velo_eyebrow);
        $template->set('text', $model->text);
        $template->set('btn_label', $model->velo_btn_label ?: 'Stelle melden');
        $template->set('melde_card_path', self::MELDE_CARD_PATHS[$model->velo_melde_card] ?? null);

        return $template->getResponse();
    }
}
