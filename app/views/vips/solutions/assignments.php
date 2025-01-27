<?php
/**
 * @var array $test_data
 * @var string $course_id
 */
?>
<? if (count($test_data['assignments'])): ?>
    <? if (VipsModule::hasStatus('tutor', $course_id)): ?>
        <?= $this->render_partial('vips/solutions/assignments_list', $test_data) ?>
    <? else: ?>
        <?= $this->render_partial('vips/solutions/assignments_list_student', $test_data) ?>
        <? if (isset($overview_data)): ?>
            <?= $this->render_partial('vips/solutions/student_grade', $overview_data) ?>
        <? endif ?>
    <? endif ?>
<? else: ?>
    <?= MessageBox::info(_('Es ist kein beendetes Aufgabenblatt vorhanden.')) ?>
<? endif ?>
