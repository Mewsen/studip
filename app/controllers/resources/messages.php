<?php

/**
 * messages.php - contains Resources_MessagesController
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @copyright   2018-2019
 * @category    Stud.IP
 * @since       4.5
 */


/**
 * Resources_MessagesController contains actions
 * for the message sending functions related to the resource
 * management system.
 */
class Resources_MessagesController extends AuthenticatedController
{
    /**
     * return Room[]
     */
    protected function buildRoomList(array $room_ids) : array
    {
        $rooms = [];
        if (count($room_ids) > 0) {
            Room::findEachMany(
                function (Room $room) use (&$rooms) {
                    // The current user must have at least user permissions to preselect a room.
                    if (!$room->userHasPermission($this->current_user)) {
                        return;
                    }

                    // Set the room as selected room and put its ID in the new room-ID list.
                    $rooms[$room->id] = $room;
                },
                $room_ids,
                'ORDER BY name'
            );
        }
        return $rooms;
    }

    public function index_action()
    {
        if (Navigation::hasItem('/resources/messages/index')) {
            Navigation::activateItem('/resources/messages/index');
        }
        PageLayout::setTitle(_('Rundmails senden'));

        $this->current_user = User::findCurrent();

        if (
            !ResourceManager::userHasGlobalPermission($this->current_user)
            && !RoomManager::userHasRooms($this->current_user)
        ) {
            //The user has neither global nor specific user permissions on resources.
            throw new AccessDeniedException();
        }

        $this->room_selection = 'search';
        $this->recipient_selection = 'permission';
        $this->clipboard_id = '';
        $this->min_permission = '';
        $initial_room_ids     = Request::getArray('room_ids');
        $this->selected_rooms = $this->buildRoomList($initial_room_ids);

        $this->room_search = new QuickSearch(
            'room_name',
            new RoomSearch()
        );
        $this->room_search->fireJSFunctionOnSelect(
            'STUDIP.Resources.Messages.selectRoom'
        );

        $this->clipboards = Clipboard::getClipboardsForUser(
            $GLOBALS['user']->id,
            ['Room']
        );

        //STUB
        $this->begin = new DateTime();

        $this->end = clone $this->begin;
        $this->end->add(new DateInterval('P14D'));

        if (Request::submitted('write_mail')) {
            CSRFProtection::verifyUnsafeRequest();

            $this->recipient_selection = Request::get('recipient_selection');
            $this->room_selection = Request::get('room_selection');
            $this->clipboard_id = Request::int('clipboard_id');

            //First validation:

            if (!in_array($this->room_selection, ['search', 'clipboard'])) {
                PageLayout::postError(
                    _('Die Raumauswahl ist ungültig!')
                );
                return;
            }

            if (!in_array($this->recipient_selection, ['permission', 'booking'])) {
                PageLayout::postError(
                    _('Der Empfängerkreis ist ungültig!')
                );
                return;
            }

            //Load fields depending on the selection method
            //selected for rooms and recipients.
            if ($this->recipient_selection == 'permission') {
                $this->min_permission = Request::get('min_permission');

                //Second validation:

                if (!in_array($this->min_permission, ['user', 'autor', 'tutor', 'admin'])) {
                    PageLayout::postError(
                        _('Die Rechtestufe ist ungültig!')
                    );
                    return;
                }
            } else {
                $this->begin = Request::getDateTime(
                    'begin_date',
                    'd.m.Y',
                    'begin_time',
                    'H:i'
                );
                $this->end = Request::getDateTime(
                    'end_date',
                    'd.m.Y',
                    'end_time',
                    'H:i'
                );

                //Second validation:

                if (!$this->begin) {
                    PageLayout::postError(
                        _('Der Startzeitpunkt konnte nicht verarbeitet werden!')
                    );
                    $this->relocate('resources/messages/index');
                    return;
                }

                if (!$this->end) {
                    PageLayout::postError(
                        _('Der Endzeitpunkt konnte nicht verarbeitet werden!')
                    );
                    $this->relocate('resources/messages/index');
                    return;
                }

                if ($this->begin > $this->end) {
                    PageLayout::postError(
                        _('Der Startzeitpunkt darf nicht hinter dem Endzeitpunkt liegen!')
                    );
                    $this->relocate('resources/messages/index');
                    return;
                }
            }

            //We now collect the rooms and check if all the permissions are present:
            if ($this->room_selection == 'clipboard') {
                //We need to collect the room-IDs differently than when a room is selected directly.
                $selected_clipboard = Clipboard::find($this->clipboard_id);
                if ($selected_clipboard) {
                    $initial_room_ids     = $selected_clipboard->getAllRangeIds('Room');
                    $this->selected_rooms = $this->buildRoomList($initial_room_ids);
                }
            }

            //If we haven't found any rooms with sufficient permissions here we must stop:
            if (empty($this->selected_rooms)) {
                if (empty($initial_room_ids)) {
                    PageLayout::postError(_('Sie haben keinen Raum ausgewählt.'));
                } else {
                    PageLayout::postError(_('Sie haben an den ausgewählten Räumen nicht die erforderlichen Berechtigungen, um eine Rundmail zu senden.'));
                }
                return;
            }

            //Now we get the recipients:

            $recipients = [];

            if ($this->recipient_selection == 'permission') {
                $perm_levels = ResourceManager::getHigherPermissionLevels(
                    $this->min_permission
                );
                array_push($perm_levels, $this->min_permission);

                $now = time();

                $recipients = User::findBySql(
                    "user_id IN (
                        SELECT user_id
                        FROM resource_permissions
                        WHERE
                        resource_id IN ( :room_ids )
                        AND
                        perms IN ( :perms )
                        UNION
                        SELECT user_id
                        FROM resource_temporary_permissions
                        WHERE
                        resource_id IN ( :room_ids )
                        AND
                        perms in ( :perms )
                        AND
                        begin <= :now
                        AND
                        end >= :now
                    )",
                    [
                        'room_ids' => array_keys($this->selected_rooms),
                        'perms' => $perm_levels,
                        'now' => $now
                    ]
                );
            } elseif ($this->recipient_selection == 'booking') {
                foreach ($this->selected_rooms as $room) {
                    $relevant_room_bookings = ResourceBooking::findByResourceAndTimeRanges(
                        $room,
                        [
                            [
                                'begin' => $this->begin,
                                'end' => $this->end
                            ]
                        ]
                    );

                    foreach ($relevant_room_bookings as $booking) {
                        $users = $booking->getAssignedUsers(false);
                        foreach ($users as $user) {
                            $recipients[$user->id] = $user;
                        }
                    }
                }
            }
            if (!$recipients) {
                PageLayout::postInfo(
                    _('Für die gewählten Räume gibt es keine Empfänger!')
                );
                if (!Request::isDialog()) {
                    $this->relocate('resources/messages/index');
                }
                return;
            }

            //Send the mail to each recipient in the recipient's language:

            $recipient_array = [];
            foreach ($recipients as $recipient) {
                $recipient_array[] = $recipient->username;
            }

            $_SESSION['sms_data']['p_rec'] = array_unique($recipient_array);
            $this->redirect(
                'messages/write'
            );
        }
    }
}
