<?php

/*
 *  Copyright (c) 2026  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

class CoreEvaluation extends CorePlugin implements StudipModuleExtended
{
    use IconNavigationTrait;

    public function getManyIconNavigation(array $course_ids, ?string $user_id = null): array
    {
        if (!$this->isTabActive()) {
            return [];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTabNavigation($course_id)
    {
        if (!$this->isTabActive()) {
            return null;
        }

        $navigation = new Navigation(_('Evaluation'), 'dispatch.php/course/evaluation');
        $navigation->setImage(Icon::create('evaluation', Icon::ROLE_INFO_ALT));
        $navigation->setActiveImage(Icon::create('evaluation', Icon::ROLE_INFO));

        return ['evaluation' => $navigation];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return [
            'summary' => _('Lehrveranstaltungs-Evaluationen'),
            'description' => _(''),
            'displayname' => _('Evaluation'),
            'category' => _('Lehr- und Lernorganisation'),
            'keywords' => _(''),
            'descriptionshort' => _(''),
            'descriptionlong' => _(''),
            'icon' => Icon::create('evaluation', Icon::ROLE_INFO),
            'icon_clickable' => Icon::create('evaluation'),
//            'screenshots' => [
//                'path' => 'assets/images/plus/screenshots/Informationen',
//                'pictures' => [
//                    0 => ['source' => 'Zwei_Eintraege_mit_Inhalten_zur_Verfuegung_stellen.jpg', 'title' => _('Zwei Einträge mit Inhalten zur Verfügung stellen')],
//                    1 => ['source' => 'Neue_Informationsseite_anlegen.jpg', 'title' => _('Neue Informationsseite anlegen')],
//                    2 => ['source' => 'Informationsseite_bearbeiten.jpg', 'title' => _('Informationsseite bearbeiten')]
//                ]
//            ]
        ];
    }

    public function getInfoTemplate($course_id)
    {
        return null;
    }

    public function isTabActive(): bool
    {
        $evaluation = QuestionnaireEvalAssignment::findOneBySQL(
            "`course_id` = ? AND `applied` = 1 ORDER BY `startdate`",
            [Context::getId()]);
        return PluginManager::getInstance()->getPlugin(CoreEvaluation::class) && $evaluation &&
                (User::findCurrent()->hasPermissionLevel('tutor', Context::get()) ||
                $evaluation->startdate <= time());
    }
}
