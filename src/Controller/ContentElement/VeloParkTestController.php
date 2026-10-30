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

#[AsContentElement('velo_park_test', category: 'velo', template: 'content_element/velo_park_test')]
class VeloParkTestController extends AbstractContentElementController
{
    /** Rating scale colors (red -> green), shared with the public report map's pin legend. */
    private const NOTE_COLOR_STEPS = [
        2.0 => 'var(--color-rate-1)',
        3.0 => 'var(--color-rate-2)',
        4.0 => 'var(--color-rate-3)',
        5.0 => 'var(--color-rate-4)',
    ];
    private const NOTE_COLOR_MAX = 'var(--color-rate-5)';

    /**
     * Mirrors the option labels defined in contao/dca/tl_content.php for each velo_pt_crit_*
     * field. Kept here rather than read from $GLOBALS['TL_DCA'] at render time, since that array
     * is only guaranteed to be loaded in the backend/editing context, not on every frontend
     * request.
     */
    private const CRITERIA_OPTIONS = [
        'velo_pt_crit_access' => ['gut' => 'gut', 'mittel' => 'mittel', 'schlecht' => 'schlecht'],
        'velo_pt_crit_steps_entrance' => ['lt50' => '<50', '50-100' => '50-100', '100plus' => '100+'],
        'velo_pt_crit_steps_carts' => ['lt50' => '<50', '50-100' => '50-100', '100plus' => '100+'],
        'velo_pt_crit_trailer_space' => ['ja' => 'ja', 'knapp' => 'knapp', 'nein' => 'nein'],
        'velo_pt_crit_free_spaces' => ['ja' => 'ja', 'teils' => 'teils', 'nein' => 'nein'],
        'velo_pt_crit_weather' => ['ja' => 'ja', 'wenige' => 'wenige', 'nein' => 'nein'],
    ];

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->addDefaultDataToTemplate($template, $model->row());

        $template->set('lead', $model->velo_lead);

        $highlights = StringUtil::deserialize($model->velo_pt_highlights, true);
        $template->set('highlights', $highlights);

        $template->set('explanation', $model->velo_pt_explanation);

        $note = $model->velo_pt_note;
        $template->set('note', $note);
        $template->set('note_color', $this->noteColor($note));

        /* Two groups, agreed with the user: "getting there" vs. "the parking spot itself" --
           see the design-tokens repo discussion for the reasoning behind this particular split. */
        $template->set('criteria_groups', [
            [
                'label' => 'Lage & Erreichbarkeit',
                'items' => [
                    ['label' => 'Erreichbarkeit, Anfahrt', 'value' => $this->fieldLabel('velo_pt_crit_access', $model->velo_pt_crit_access)],
                    ['label' => 'Schritte bis zum Eingang', 'value' => $this->fieldLabel('velo_pt_crit_steps_entrance', $model->velo_pt_crit_steps_entrance)],
                    ['label' => 'Schritte bis zu den Einkaufswagen', 'value' => $this->fieldLabel('velo_pt_crit_steps_carts', $model->velo_pt_crit_steps_carts)],
                ],
            ],
            [
                'label' => 'Vor Ort',
                'items' => [
                    ['label' => 'Platz für Veloanhänger & Cargobikes', 'value' => $this->fieldLabel('velo_pt_crit_trailer_space', $model->velo_pt_crit_trailer_space)],
                    ['label' => 'Ausreichend Plätze während Stosszeiten', 'value' => $this->fieldLabel('velo_pt_crit_free_spaces', $model->velo_pt_crit_free_spaces)],
                    ['label' => 'Wettergeschützte Velo-Parkplätze', 'value' => $this->fieldLabel('velo_pt_crit_weather', $model->velo_pt_crit_weather)],
                ],
            ],
        ]);

        $template->set('address', $model->velo_pt_address);
        $template->set('lat', $model->velo_pt_lat);
        $template->set('lng', $model->velo_pt_lng);

        return $template->getResponse();
    }

    private function noteColor(string $note): string
    {
        $value = (float) $note;

        foreach (self::NOTE_COLOR_STEPS as $threshold => $color) {
            if ($value < $threshold) {
                return $color;
            }
        }

        return self::NOTE_COLOR_MAX;
    }

    private function fieldLabel(string $field, string $value): string
    {
        if ('' === $value) {
            return '';
        }

        return self::CRITERIA_OPTIONS[$field][$value] ?? $value;
    }
}
