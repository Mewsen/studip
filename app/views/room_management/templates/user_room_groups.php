<?php
/**
 * @var string $url
 * @var SimpleORMapCollection<Clipboard> $user_groups
 * @var Clipboard $active_group
 * @var SimpleORMapCollection<Room> $rooms
 * @var Array $params
 */
?>

<div class="user-room-groups-widget">
    <form action="<?= URLHelper::getLink($url) ?>" class="default" method="get">
        <select name="clipboard_id" class="submit-upon-select">
            <?php foreach ($user_groups as $user_group): ?>
                <option value="<?= $user_group->id ?>" <?= $user_group->id === $active_group->id ? 'selected' : '' ?>>
                    <?= $user_group->name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <ul class="user-room-groups-widget__rooms">
        <?php foreach ($rooms as $room): ?>
            <li class="user-room-groups-widget__room-item">
                <?= $room->name ?>
                <div class="actions">
                    <a href="<?= Room::getLinkForAction('show', $room->id) ?>" data-dialog>
                        <?= Icon::create('info-circle')->asImg([
                            'title' => _('Rauminformationen'),
                            'class' => 'text-bottom'
                        ])?>
                    </a>
                    <a href="<?= Room::getLinkForAction('semester_plan', $room->id) ?>" target="_blank">
                        <?= Icon::create('timetable')->asImg([
                            'title' => _('Semesterbelegung'),
                            'class' => 'text-bottom'
                        ])?>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
