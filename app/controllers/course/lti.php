<?php

use Lti\Deployment;
use Lti\ResourceLink;

/**
 * course/lti.php - LTI Resources
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Course_LtiController extends StudipController
{
    protected $with_session = true;
    protected bool $isModerator = false;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->range_id = Context::getId();
        $this->course = Course::find($this->range_id);

        $this->isModerator = LtiToolModule::isModerator($this->range_id);

        if (Navigation::hasItem('/course/lti/index')) {
            Navigation::activateItem('/course/lti/index');
        }
    }

    /**
     * Display the list of LTI Resources blocks.
     */
    public function index_action(): void
    {
        Helpbar::get()->addPlainText('', _('Auf dieser Seite können Sie externe Anwendungen einbinden, sofern diese den LTI-Standard (Version 1.x order 1.3a) unterstützen.'));

        if ($this->isModerator) {
            $widget = Sidebar::get()->addWidget(new ActionsWidget());

            $widget->addLink(
                _('LTI-Ressource hinzufügen'),
                $this->url_for('admin/lti/resources/create'),
                Icon::create('add')
            );
        }

        //Check for error messages:
        if (Request::get('deployment_id') && (Request::submitted('lti_msg') || Request::submitted('lti_errormsg'))) {
            $deployment = Deployment::findOneBySQL("deployment_key = ?", [Request::get('deployment_id')]);
            if ($deployment) {
                //Get the resource link for the deployment and display the messages:
                $resourceLink = ResourceLink::findOneBySQL(
                    "`deployment_id` = :deployment_id AND `course_id` = :course_id",
                    [
                        'deployment_id' => $deployment->id,
                        'course_id' => $this->range_id
                    ]
                );

                if ($resourceLink) {
                    if (Request::get('lti_msg')) {
                        PageLayout::postInfo(htmlReady($resourceLink->title . ': ' . Request::get('lti_msg')));
                    }
                    if (Request::get('lti_errormsg')) {
                        PageLayout::postError(htmlReady($resourceLink->title . ': ' . Request::get('lti_errormsg')));
                    }
                }
            }
        }

        $this->render_vue_app(
            Studip\VueApp::create('lti/resources/Index')
        );
    }

    /**
     * Display the (simple) LTI grade book.
     */
    public function grades_action()
    {
        Navigation::activateItem('/course/lti/grades');

        if ($this->isModerator) {
            $this->lti_data_array = ResourceLink::findBySQL(
                "`course_id` = :course_id
                ORDER BY `position`",
                ['course_id' => $this->range_id]
            );
        } else {
            //Only load those deployments that are fully configured:
            $this->lti_data_array = ResourceLink::findBySQL(
                "`course_id` = :course_id
                AND (`options` IS NULL OR `options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `position`",
                ['course_id' => $this->range_id]
            );
        }

        if ($this->isModerator) {
            $this->desc = Request::int('desc');
            $this->members = CourseMember::findByCourseAndStatus($this->range_id, 'autor');

            if ($this->desc) {
                $this->members = array_reverse($this->members);
            }

            $widget = Sidebar::get()->addWidget(new ExportWidget());
            $widget->addLink(
                _('Ergebnisse exportieren'),
                $this->url_for('course/lti/export_grades'),
                Icon::create('download')
            );
        } else {
            $this->render_action('grades_user');
        }

        Helpbar::get()->addPlainText('', _('Auf dieser Seite können Sie die Ergebnisse sehen, die von LTI-Tools zurückgemeldet wurden.'));
    }

    /**
     * Export grades from the gradebook in CSV format.
     */
    public function export_grades_action()
    {
        if ($this->isModerator) {
            $lti_data_array = ResourceLink::findByCourse_id($this->range_id, 'ORDER BY position');
        } else {
            //Only load those deployments that are fully configured:
            $lti_data_array = ResourceLink::findBySQL(
                "`course_id` = :course_id AND (`options` IS NULL OR `options` NOT LIKE '%unfinished_deep_linking%')
                ORDER BY `position`",
                ['course_id' => $this->range_id]
            );
        }

        $columns = [_('Nachname'), _('Vorname')];

        // add one column for each LTI tool block
        foreach ($lti_data_array as $lti_data) {
            $columns[] = $lti_data->title;
        }

        $data = [$columns];

        foreach (CourseMember::findByCourseAndStatus($this->range_id, 'autor') as $member) {
            $row = [$member->nachname, $member->vorname];

            foreach ($lti_data_array as $lti_data) {
                if ($grade = $lti_data->grades->findOneBy('user_id', $member->user_id)) {
                    $row[] = (float) $grade->score;
                } else {
                    $row[] = '';
                }
            }

            $data[] = $row;
        }

        $filename = Context::get()->name . ' - ' . _('Ergebnisse') . '.csv';
        $this->render_csv($data, $filename);
    }
}
