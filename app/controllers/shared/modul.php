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
    public function __construct(Trails\Dispatcher $dispatcher)
    {
        $this->allow_nobody = Config::get()->getValue('COURSE_SEARCH_IS_VISIBLE_NOBODY');

        parent::__construct($dispatcher);
    }

    public function overview_action($modul_id, $semester_id = null)
    {
        $display_language = Request::option('display_language', $_SESSION['_language']);
        ModuleManagementModel::setContentLanguage($display_language);

        $this->modul = Modul::find($modul_id);
        if (!$this->modul) {
            PageLayout::postError(_('Unbekanntes Modul'));
            return;
        }
        if (!$this->modul->hasPublicStatus()) {
            throw new AccessDeniedException();
        }
        $this->type = 1;
        if (count($this->modul->modulteile) < 2) {
            $this->type = 3;
            $modulteil = $this->modul->modulteile->first();
            if ($modulteil && count($modulteil->lvgruppen) > 0) {
                $this->type = 2;
            }
        }

        if (!$semester_id) {
            $this->semester = Semester::findDefault();
        } else {
            $this->semester = Semester::find($semester_id);
        }

        $abschnitt_id = Request::option('abschnitt_id');
        $this->code = '';
        $this->title = '';
        $abschnitt_modul = $this->modul->abschnitte_modul->findOneBy('abschnitt_id', $abschnitt_id);
        if ($abschnitt_modul) {
            $this->modul->setReplaceDfAbschnitt($abschnitt_modul->abschnitt);
            $this->code = trim($abschnitt_modul->modulcode);
            $this->title = trim($abschnitt_modul->bezeichnung);
        }

        $this->pruef_ebene = $GLOBALS['MVV_MODUL']['PRUEF_EBENE']['values'][$this->modul->pruef_ebene]['name'] ?? null;
        PageLayout::setTitle($this->modul->getDisplayName() . ' (' . _('Veranstaltungsübersicht') .')');
    }

    public function description_action($id)
    {
        $this->modul = Modul::find($id);
        $perm = MvvPerm::get($this->modul);
        if (!($this->modul->hasPublicStatus() || $perm->haveObjectPerm(MvvPerm::PERM_READ))) {
            throw new AccessDeniedException();
        }
        $this->type = 1;
        if (count($this->modul->modulteile) < 2) {
            $this->type = 3;
            $modulteil = $this->modul->modulteile->first();
            if ($modulteil && count($modulteil->lvgruppen) > 0) {
                $this->type = 2;
            }
        }

        $this->display_language = Request::get('display_language', $this->modul->original_language);
        ModuleManagementModel::setContentLanguage($this->display_language);
        I18NString::setDefaultLanguage($this->modul->original_language);
        I18NString::setContentLanguage($this->display_language);

        $this->start_semester = Semester::findByTimestamp($this->modul->start);

        if (!$this->modul->responsible_institute) {
            $this->institute_name = null;
        } elseif ($this->modul->responsible_institute->institute) {
            $this->institute_name = $this->modul->responsible_institute->institute->name;
        } else {
            $this->institute_name = _('Unbekannte Einrichtung');
        }
        $abschnitt_id = Request::option('abschnitt_id');
        $this->code = '';
        $this->title = '';
        $this->abschnitt_modul = $this->modul->abschnitte_modul->findOneBy('abschnitt_id', $abschnitt_id);
        if ($this->abschnitt_modul) {
            $this->modul->setReplaceDfAbschnitt($this->abschnitt_modul->abschnitt);
            $this->code = trim($this->abschnitt_modul->modulcode);
            $this->title = trim($this->abschnitt_modul->bezeichnung);
        }
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
