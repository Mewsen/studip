<?php
/**
 * vips/config.php - global configuration controller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Vips_ConfigController extends AuthenticatedController
{
    /**
     * Callback function being called before an action is executed. If this
     * function does not return FALSE, the action will be called, otherwise
     * an error will be generated and processing will be aborted. If this function
     * already #rendered or #redirected, further processing of the action is
     * withheld.
     *
     * @param string  Name of the action to perform.
     * @param array   An array of arguments to the action.
     *
     * @return bool|void
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $GLOBALS['perm']->check('root');

        Navigation::activateItem('/admin/config/vips');
        PageLayout::setHelpKeyword('Basis.VipsEinstellungen');
        PageLayout::setTitle(_('Einstellungen für Aufgaben'));
    }

    public function index_action()
    {
        $this->fields = DataField::getDataFields('user');
        $this->config = Config::get();

        $widget = new ActionsWidget();
        $widget->addLink(
            _('Anstehende Klausuren anzeigen'),
            $this->pending_assignmentsURL(),
            Icon::create('doctoral_cap')
        )->asDialog('size=big');
        Sidebar::get()->addWidget($widget);
    }

    public function save_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $exam_mode = Request::int('exam_mode', 0);
        $exam_terms = trim(Request::get('exam_terms'));
        $exam_terms = Studip\Markup::purifyHtml($exam_terms);

        $config = Config::get();
        $config->store('VIPS_EXAM_RESTRICTIONS', $exam_mode);
        $config->store('VIPS_EXAM_TERMS', $exam_terms);

        $room = Request::getArray('room');
        $ip_range = Request::getArray('ip_range');
        $ip_ranges = [];

        foreach ($room as $i => $name) {
            $name = preg_replace('/[ ,]+/', '_', trim($name));

            if ($name !== '') {
                $ip_ranges[$name] = trim($ip_range[$i]);
            }
        }

        if ($ip_ranges) {
            ksort($ip_ranges);
            $config->store('VIPS_EXAM_ROOMS', $ip_ranges);
        }

        PageLayout::postSuccess(_('Die Einstellungen wurden gespeichert.'));

        $this->redirect('vips/config');
    }

    public function pending_assignments_action()
    {
        $this->assignments = VipsAssignment::findBySQL(
            "range_type = 'course' AND type = 'exam' AND
             start BETWEEN UNIX_TIMESTAMP(NOW() - INTERVAL 1 DAY) AND UNIX_TIMESTAMP(NOW() + INTERVAL 14 DAY) AND end > UNIX_TIMESTAMP()
             ORDER BY start"
        );
    }
}
