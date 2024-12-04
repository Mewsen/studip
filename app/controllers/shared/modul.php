<?php
/**
 * modul.php - Shared_ModulController
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Peter Thienel <thienel@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       3.5
 */

class Shared_ModulController extends AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        $this->allow_nobody = Config::get()->COURSE_SEARCH_IS_VISIBLE_NOBODY;

        parent::before_filter($action, $args);
    }

    public function overview_action($modul_id, $semester_id = null)
    {
        $display_language = Request::option('display_language', $_SESSION['_language']);
        ModuleManagementModel::setLanguage($display_language);

        $this->modul = Modul::find($modul_id);
        if (!$this->modul->hasPublicStatus()) {
            throw new AccessDeniedException();
        }
        if ($this->modul) {
            $this->details_id = $this->modul->getId();

            $type = 1;
            if (count($this->modul->modulteile) == 1) {
                $modulteil = $this->modul->modulteile->first();
                $type = 3;
                if (count($modulteil->lvgruppen) > 0) {
                    $type = 2;
                }
            } else if (count($this->modul->modulteile) == 0) {
                $type = 3;
            }

            if (!$semester_id) {
                $current_semester = Semester::findDefault();
            } else {
                $current_semester = Semester::find($semester_id);
            }

            $sws = 0;
            $institut = new Institute($this->modul->responsible_institute->institut_id);
            $modulteile_data = [];
            foreach ($this->modul->modulteile as $modulteil) {
                $modulteil_deskriptor = $modulteil->getDeskriptor();
                $sws += (int) $modulteil->sws;
                $num_bezeichnung = $GLOBALS['MVV_MODULTEIL']['NUM_BEZEICHNUNG']['values'][$modulteil->num_bezeichnung]['name'] ?? '';
                $name_kurz = sprintf('%s %d', $num_bezeichnung, $modulteil->nummer);
                $modulteile_data[$modulteil->getId()] = [
                    'name' => $modulteil->getDisplayName(),
                    'name_kurz' => $name_kurz,
                    'voraussetzung' => $modulteil_deskriptor->voraussetzung,
                    'pruef_leistung' => $modulteil_deskriptor->pruef_leistung,
                    'pruef_vorleistung' => $modulteil_deskriptor->pruef_vorleistung,
                    'kommentar' => $modulteil_deskriptor->kommentar,
                    'kapazitaet' => $modulteil->kapazitaet,
                    'lvGruppen' => []
                ];

                $lvGruppen = Lvgruppe::findByModulteil($modulteil->getId());
                foreach ($lvGruppen as $lvGruppe) {
                    $ids = array_column($lvGruppe->getAssignedCoursesBySemester($current_semester['semester_id'], $GLOBALS['user']->id), 'seminar_id');
                    $courses = Course::findMany($ids, 'order by Veranstaltungsnummer, Name');
                    $modulteile_data[$modulteil->getId()]['lvGruppen'][$lvGruppe->getId()] = [
                        'courses' => $courses,
                        'alt_texte' => $lvGruppe->alttext
                    ];
                }
            }
            $this->modulteile = $modulteile_data;
            $this->deskriptor = $this->modul->getDeskriptor();
            $this->institut = $institut;
            $this->semester = $current_semester;
            $this->sws = $sws;

            $this->pruef_ebene = $GLOBALS['MVV_MODUL']['PRUEF_EBENE']['values'][$this->modul->pruef_ebene]['name'] ?? null;
            $this->type = $type;
            $this->self_url = $this->url_for('modul/show/' . $modul_id);
            $this->detail_url = $this->url_for('modul/detail/' . $modul_id);
            PageLayout::setTitle($this->modul->getDisplayName() . ' (' . _('Veranstaltungsübersicht') .')');
        }
    }

    public function description_action($id)
    {
        $this->modul = Modul::find($id);
        $perm = MvvPerm::get($this->modul);
        if (!($this->modul->hasPublicStatus() || $perm->haveObjectPerm(MvvPerm::PERM_READ))) {
            throw new AccessDeniedException();
        }
        $this->type = 1;
        if (count($this->modul->modulteile) == 1) {
            $modulteil = $this->modul->modulteile->first();
            $this->type = 3;
            if (count($modulteil->lvgruppen) > 0) {
                $this->type = 2;
            }
        } else if (count($this->modul->modulteile) == 0) {
            $this->type = 3;
        }

        if (!Request::get('sem_select')) {
            $currentSemester = Semester::findCurrent();
        } else {
            $currentSemester = Semester::find(Request::get('sem_select'));
        }

        $this->display_language = Request::get('display_language', $this->modul->original_language);
        ModuleManagementModel::setLanguage($this->display_language);
        I18NString::setDefaultLanguage($this->modul->original_language);
        I18NString::setContentLanguage($this->display_language);

        $this->semesterSelector = Semester::getSemesterSelector(null, $currentSemester['semester_id'], 'semester_id', false);
        $this->pruefungsEbene = isset($GLOBALS['MVV_MODUL']['PRUEF_EBENE']['values'][$this->modul->pruef_ebene])
                              ? $GLOBALS['MVV_MODUL']['PRUEF_EBENE']['values'][$this->modul->pruef_ebene]['name']
                              : null;
        $this->modulDeskriptor = $this->modul->getDeskriptor();
        $this->startSemester = Semester::findByTimestamp($this->modul->start);

        if (!$this->modul->responsible_institute) {
            $this->instituteName = null;
        } elseif ($this->modul->responsible_institute->institute) {
            $this->instituteName = $this->modul->responsible_institute->institute->name;
        } else {
            $this->instituteName = _('Unbekannte Einrichtung');
        }
        $this->semester = $currentSemester;
        PageLayout::setTitle($this->modul->getDisplayName() . ' (' . _('Vollständige Modulbeschreibung') .')');
    }

    public function mail_action($modul_id, $semester_id)
    {
        if ($GLOBALS['perm']->have_perm('admin')) {
            $stm = DBManager::get()->prepare('SELECT DISTINCT auth_user_md5.username FROM auth_user_md5
                JOIN seminar_user ON (auth_user_md5.user_id = seminar_user.user_id)
                JOIN seminare ON (seminare.seminar_id = seminar_user.seminar_id)
                LEFT JOIN semester_courses ON (seminare.seminar_id = semester_courses.course_id)
                JOIN mvv_lvgruppe_seminar ON (mvv_lvgruppe_seminar.seminar_id = seminare.seminar_id)
                JOIN mvv_lvgruppe_modulteil ON (mvv_lvgruppe_modulteil.lvgruppe_id = mvv_lvgruppe_seminar.lvgruppe_id)
                JOIN mvv_modulteil ON (mvv_modulteil.modulteil_id = mvv_lvgruppe_modulteil.modulteil_id)
                WHERE mvv_modulteil.modul_id = :modul_id
                AND seminar_user.status = :status
                AND (semester_courses.semester_id = :semester_id OR semester_courses.semester_id IS NULL)');
            $stm->execute(['modul_id' => $modul_id, 'status' => 'autor', ':semester_id' => $semester_id]);
            $_SESSION['sms_data']['p_rec'] = $stm->fetchFirst();

            $this->redirect(URLHelper::getURL('dispatch.php/messages/write'));
        } else {
            throw new AccessDeniedException();
        }
    }
}
