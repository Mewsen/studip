<?php

/**
 * quick.php Controller for quick creation of massmails to selected courses.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl
 * @license     GPL2 or any later version
 * @since       Stud.IP 6.0
 */

class Massmail_QuickController extends \AuthenticatedController
{

    public function courses_action()
    {
        $GLOBALS['perm']->check('admin');

        Navigation::activateItem('/messaging/massmail/message');
        PageLayout::setTitle(_('Nachricht an Zielgruppe schreiben'));

        $message = new \MassMail\MassMailMessage();
        $message->target = 'courses';
        $message->sender_id = $message->author_id = User::findCurrent()->id;
        $message->config = ['perm' => 'autor', 'courses' => Request::optionArray('courses')];

        $courses = Request::optionArray('courses');

        $form = \Studip\Forms\Form::fromSORM(
            $message,
            [
                'legend' => _('Grunddaten'),
                'collapsed' => false,
                'collapsable' => false,
                'fields' => [
                    'courses' => [
                        'type' => 'hidden',
                        'value' => implode(',', $courses),
                        'store' => function($value, $input) {
                            $input->getContextObject()->config = [];
                            $input->getContextObject()->config['courses'] = explode(',', $value);
                        }
                    ],
                    'course_perm' => [
                        'type' => 'select',
                        'label' => _('Berechtigungsebene wählen'),
                        'value' => 'autor',
                        'options' => [
                            'dozent' => get_title_for_status('dozent', 2, 1),
                            'tutor' => get_title_for_status('tutor', 2, 1),
                            'autor' => get_title_for_status('autor', 2, 1),
                            'user' => get_title_for_status('user', 2, 1),
                        ],
                        'store' => function($value, $input) {
                            $input->getContextObject()->config['perm'] = $value;
                        }
                    ],
                    'subject' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => _('Betreff'),
                        'value' => $message->subject
                    ],
                    'message' => [
                        'type' => 'serialWysiwyg',
                        'required' => true,
                        'label' => _('Nachricht'),
                        'value' => $message->message,
                        'markers' => json_encode(
                            array_map(
                                fn ($m) => $m->toArray(),
                                \MassMail\MassMailMarker::findAll(
                                    \MassMail\MassMailPermission::has(User::findCurrent()->id, true)
                                )
                            )
                        )
                    ]
                ]
            ],
            $this->url_for('admin/courses')
        )->autoStore();

        $this->render_form($form);
    }

}
