<?php
/**
 * vips/exam_mode.php - restricted exam mode controller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Vips_ExamModeController extends AuthenticatedController
{
    /**
     * Display a list of courses with currently active tests of type 'exam'.
     * Only used when there are multiple courses with running exams.
     */
    public function index_action()
    {
        PageLayout::setTitle(_('Klausurübersicht'));

        Helpbar::get()->addPlainText('',
            _('Der normale Betrieb von Stud.IP ist für Sie zur Zeit gesperrt, da Klausuren geschrieben werden.'));

        $this->courses = VipsModule::getCoursesWithRunningExams($GLOBALS['user']->id);
    }
}
