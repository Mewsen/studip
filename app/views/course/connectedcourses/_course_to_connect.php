<tr>
    <td>
        <?= CourseAvatar::getAvatar($course->id)->getImageTag(Avatar::SMALL) ?>
        <?= htmlReady($course->getFullName()) ?>
    </td>
    <td>
        <? if ($course->start_semester) : ?>
            <?= htmlReady($course->start_semester->name) ?>
            <? if ($course->end_semester && $course->end_semester->id !== $course->start_semester->id) : ?>
                - <?= htmlReady($course->end_semester->name) ?>
            <? endif ?>
        <? endif ?>
    </td>
    <td class="actions">
        <?= Icon::create('add')->asInput([
            'title' => _('Verknüpfung mit dieser Veranstaltung vorschlagen.'),
            'formaction' => $controller->connectURL($course->id),
            'formmethod' => 'post'
        ]) ?>
    </td>
</tr>
