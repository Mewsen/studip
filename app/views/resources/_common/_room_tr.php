<?
/**
 * This is a specialisation of the _resource_tr template for rooms.
 *
 * Template variables:
 *
 * @var Room $room A Room object.
 * @var bool $show_admin_actions Whether to display actions which are
 *     designed for users with 'admin' resource permissions.
 *     Defaults to false (do not show actions).
 * @var bool $show_tutor_actions Whether to display actions which are
 *     designed for users with 'tutor' resource permissions.
 *     Defaults to false (do not show actions).
 * @var bool $show_autor_actions Whether to display actions which are
 *     designed for users with 'autor' resource permissions.
 *     Defaults to false (do not show actions).
 * @var bool $show_user_actions Whether to display actions which are
 *     designed for users with 'user' resource permissions.
 *     Defaults to false (do not show actions).
 * @var bool $user_has_booking_rights Whether the user for which this template
 *     is rendered has booking rights on the resource (true) or not (false).
 * @var bool $show_picture Whether to display the room picture or not.
 *     Defaults to false (do not show picture).
 * @var array $additional_properties Additional properties
 *     that shall be displayed in extra columns.
 * @var array $additional_columns Additional columns for the table.
 * @var array $additional_actions Additional actions for the action menu.
 *     This array contains associative arrays where each of those arrays
 *     has the following structure and indexes:
 *     [
 *         0 => Link
 *         1 => Label
 *         2 => Icon
 *         3 => Link attributes
 *     ]
 */
?>

<?
$room_actions = [];
if ($room->requestable && $show_autor_actions) {
    $room_actions = [
        '0071' => [
            $room->getActionLink('request_list'),
            _('Anfragen auflösen'),
            Icon::create('room-request'),
            ['target' => '_blank']
        ]
    ];
}
if ($show_user_actions) {
    $room_actions['0021'] = [
        URLHelper::getLink('dispatch.php/resources/messages/index', ['room_ids[]' => $room->id]),
        _('Rundmail schreiben'),
        Icon::create('mail'),
        ['data-dialog' => 'size=auto']
    ];
}
?>

<?= $this->render_partial(
    'resources/_common/_resource_tr.php',
    [
        'checkbox_data' => $checkbox_data ?? '',
        'resource' => $room,
        'booking_plan_link_on_name' => true,
        'resource_tooltip' => $room_tooltip ?? '',
        'show_global_admin_actions' => !empty($show_global_admin_actions),
        'show_admin_actions' => $show_admin_actions,
        'show_tutor_actions' => $show_tutor_actions,
        'show_autor_actions' => $show_autor_actions,
        'show_user_actions' => $show_user_actions,
        'user_has_booking_rights' => !empty($user_has_booking_rights),
        'show_picture' => true,
        'show_full_name' => false,
        'additional_properties' => ['seats'],
        'clipboard_range_type' => 'Room',
        'additional_actions' => (
        (!empty($additional_actions) && is_array($additional_actions))
            ? array_merge(
                $room_actions,
                $additional_actions
            )
            : $room_actions
        )
    ]
) ?>
