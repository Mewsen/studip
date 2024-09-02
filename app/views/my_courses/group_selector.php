<?php
/**
 * @var string $course_id
 * @var string $selected_group_id
 */
?>
<? for ($i = 0; $i < 9; $i++) : ?>
    <td class="gruppe<?= $i ?> mycourses-group-selector" onclick="this.querySelector('input').checked = true;">
        <input type="radio" name="gruppe[<?= htmlReady($course_id) ?>]" value="<?= $i ?>"
               aria-label="<?= sprintf(_('Gruppe %u zuordnen'), $i + 1) ?>"
               id="course-group-<?= htmlReady($course_id) ?>-<?= $i ?>"
            <?= $selected_group_id == $i ? 'checked' : '' ?>>
        <label for="course-group-<?= htmlReady($course_id) ?>-<?= $i ?>">
            <span class="group-number"><?= $i + 1 ?></span>
            <span class="checked-icon">
                <?= Icon::create('accept', Icon::ROLE_INFO)->asImg(20) ?>
            </span>
        </label>
    </td>
<? endfor ?>
