<? if (!empty($show_entry['type']) && in_array($show_entry['type'], ['sem', 'virtual'])): ?>
    <?= $this->render_partial('calendar/schedule/_entry_course.php') ?>
    <? unset($show_entry) ?>
<? elseif (!empty($show_entry['type']) && $show_entry['type'] === 'inst'): ?>
    <?= $this->render_partial('calendar/schedule/_entry_inst.php') ?>
    <? unset($show_entry) ?>
<? else : ?>
    <?= $this->render_partial('calendar/schedule/_entry_schedule.php') ?>
<? endif ?>
