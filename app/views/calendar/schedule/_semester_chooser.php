<form method="post" class="default" action="<?= $controller->link_for(
    isset($inst_mode) && $inst_mode == true ? 'calendar/instschedule/index' : 'calendar/schedule/index'
) ?>">
    <label for="semester_id" class="sr-only"><?= _('Angezeigtes Semester') ?></label>
    <select name="semester_id" class="submit-upon-select" id="semester_id">
        <? foreach ($semesters as $semester) : ?>
            <? if ($semester['ende'] > time() - strtotime('1year 1day')) : ?>
                <option
                    value="<?= $semester['semester_id'] ?>" <?= $current_semester['semester_id'] == $semester['semester_id'] ? 'selected="selected"' : '' ?>>
                    <?= htmlReady($semester['name']) ?>
                    <?= $semester['beginn'] < time() && $semester['ende'] > time() ? _('*') : '' ?>
                </option>
            <? endif ?>
        <? endforeach ?>
    </select>
    <noscript>
        <?= Icon::create(
            'accept',
            Icon::ROLE_ACCEPT,
            ['title' => _('auswählen')]
        )->asInput(['type' => 'image', 'class' => 'middle']) ?>
    </noscript>
</form>
