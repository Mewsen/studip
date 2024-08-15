<?php
/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */
class CoreAdmin extends CorePlugin implements StudipModule
{
    /**
     * {@inheritdoc}
     */
    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        $navigation = new Navigation(_('Verwaltung'), 'dispatch.php/course/management');
        $navigation->setImage(Icon::create('admin', Icon::ROLE_CLICKABLE, ['title' => _('Verwaltung')]));
        return $navigation;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabNavigation($course_id)
    {
        $range = RangeFactory::find($course_id);

        // Given range is invalid, leave early
        if (!$range) {
            return [];
        }

        // Special case: Tutors of institutes may manage votes
        if (
            Config::get()->VOTE_ENABLE
            && $range instanceof Institute
            && !$range->isEditableByUser()
            && Seminar_Perm::get()->have_studip_perm('tutor', $range->id)
        ) {
            $navigation = $this->getRootNavigation();
            $this->addVoteNavigation($navigation);

            return ['admin' => $navigation];
        }

        // User may not edit this range
        if (!$range->isEditableByUser()) {
            return [];
        }

        // Build regular admin navigation
        $navigation = $this->getRootNavigation();

        $main = new Navigation(_('Werkzeuge'), 'dispatch.php/course/contentmodules');
        $navigation->addSubNavigation('contentmodules', $main);

        if ($range instanceof Course) {
            $item = new Navigation(_('Grunddaten'), 'dispatch.php/course/basicdata/view/' . $course_id);
            $item->setImage(Icon::create('edit'));
            $item->setDescription(_('Bearbeiten der Grundeinstellungen dieser Veranstaltung.'));
            $navigation->addSubNavigation('details', $item);

            $item = new Navigation(_('Infobild'), 'dispatch.php/avatar/update/course/' . $course_id);
            $item->setImage(Icon::create('file-pic'));
            $item->setDescription(_('Infobild dieser Veranstaltung bearbeiten oder löschen.'));
            $navigation->addSubNavigation('avatar', $item);

            $item = new Navigation(_('Studienbereiche'), 'dispatch.php/course/study_areas/show/' . $course_id);
            $item->setImage(Icon::create('module'));
            $item->setDescription(_('Zuordnung dieser Veranstaltung zu Studienbereichen für die Darstellung im Verzeichnis aller Veranstaltungen.'));
            $navigation->addSubNavigation('study_areas', $item);

            if ($range->getSemClass()['module']) {
                $item = new Navigation(_('LV-Gruppen'), 'dispatch.php/course/lvgselector/index/' . $course_id, ['list' => 'TRUE']);
                $item->setImage(Icon::create('learnmodule'));
                $item->setDescription(_('Zuordnung der Veranstaltung zu Lehrveranstaltungsgruppen um die Einordnung innerhalb des Modulverzeichnisses festzulegen.'));
                $navigation->addSubNavigation('lvgruppen', $item);
            }

            $item = new Navigation(_('Zeiten/Räume'), 'dispatch.php/course/timesrooms');
            $item->setImage(Icon::create('date'));
            $item->setDescription(_('Regelmäßige Veranstaltungszeiten, Einzeltermine und Ortsangaben ändern.'));
            $navigation->addSubNavigation('dates', $item);

            $item = new Navigation(_('Zugangsberechtigungen'), 'dispatch.php/course/admission');
            $item->setImage(Icon::create('lock-locked'));
            $item->setDescription(_('Zugangsbeschränkungen, Anmeldeverfahren oder einen Passwortschutz für diese Veranstaltung einrichten.'));
            $navigation->addSubNavigation('admission', $item);

            $item = new AutoNavigation(_('Zusatzangaben'), 'dispatch.php/admin/additional');
            $item->setImage(Icon::create('add'));
            $item->setDescription(_('Vorlagen zur Erhebung weiterer Angaben von Teilnehmenden auswählen.'));
            $navigation->addSubNavigation('additional_data', $item);

        }  // endif modules only seminars

        if (Config::get()->VOTE_ENABLE) {
            $this->addVoteNavigation($navigation);
        }
        if (Config::get()->EVAL_ENABLE) {
            $item = new Navigation(_('Evaluationen'), 'admin_evaluation.php?view=eval_sem');
            $item->setImage(Icon::create('evaluation'));
            $item->setDescription(_('Richten Sie fragebogenbasierte Umfragen und Lehrevaluationen ein.'));
            $navigation->addSubNavigation('evaluation', $item);
        }

        /*
         * Is the current SemClass available for grouping other courses?
         * -> show child management
         */
        if ($range instanceof Course) {
            $c = $range->getSemClass();
            if ($c->isGroup()) {

                $item = new Navigation(_('Unterveranstaltungen'), 'dispatch.php/course/grouping/children');
                $item->setImage(Icon::create('group', Icon::ROLE_INFO_ALT));
                $item->setActiveImage(Icon::create('group', Icon::ROLE_INFO));
                $item->setDescription(_('Ordnen Sie dieser Veranstaltung eine oder mehrere Unterveranstaltungen zu.'));
                $navigation->addSubNavigation('children', $item);

            /*
             * Check if any SemClasses with grouping functionality exist at all
             * -> show parent assignment.
             */
            } else if (count(SemClass::getGroupClasses()) > 0) {

                $item = new Navigation(_('Zuordnung zu Hauptveranstaltung'), 'dispatch.php/course/grouping/parent');
                $item->setImage(Icon::create('group', Icon::ROLE_INFO_ALT));
                $item->setActiveImage(Icon::create('group', Icon::ROLE_INFO));
                $item->setDescription(_('Ordnen Sie diese Veranstaltung einer bestehenden ' .
                    'Hauptveranstaltung zu oder lösen Sie eine bestehende Zuordnung.'));
                $navigation->addSubNavigation('parent', $item);

            }
        }

        return ['admin' => $navigation];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return [
            'displayname' => _('Verwaltung')
        ];
    }

    public function isActivatableForContext(Range $context)
    {
        return false;
    }

    public function getInfoTemplate($course_id)
    {
        // TODO: Implement getInfoTemplate() method.
        return null;
    }

    private function getRootNavigation(): Navigation
    {
        $navigation = new Navigation(_('Verwaltung'));
        $navigation->setImage(Icon::create('admin', Icon::ROLE_INFO_ALT));
        $navigation->setActiveImage(Icon::create('admin', Icon::ROLE_INFO));
        return $navigation;
    }

    private function addVoteNavigation(Navigation &$navigation): void
    {
        $item = new Navigation(_('Fragebögen'), 'dispatch.php/questionnaire/courseoverview');
        $item->setImage(Icon::create('vote'));
        $item->setDescription(_('Erstellen und bearbeiten von Fragebögen.'));
        $navigation->addSubNavigation('questionnaires', $item);
    }
}
