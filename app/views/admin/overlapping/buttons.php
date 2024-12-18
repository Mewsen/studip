<?php
/**
 * @var string $selected_view
 * @var Admin_OverlappingController $controller
 * @var MvvOverlappingConflict $conflict
 * @var Course $course
 * @var StgteilVersion $version
 */
?>
<div data-dialog-button>
    <? if ($selected_view !== 'info') : ?>
        <?= \Studip\LinkButton::create(
            _('Studienverlaufsplan'),
            $controller->version_infoURL($conflict->id),
            ['data-dialog' => 'size=auto;reload-on-close']
        ) ?>
    <? endif ?>
    <? if ($course) : ?>
        <?= Studip\LinkButton::create(
            _('Ausblenden'),
            $controller->excludeURL($conflict->id),
            ['data-dialog' => 'size=auto;reload-on-close']
        ) ?>
        <? if ($selected_view !== 'course_info') : ?>
            <?= \Studip\LinkButton::create(
                _('Veranstaltungsdetails'),
                $controller->course_infoURL($conflict->id),
                ['data-dialog' => 'size=auto;reload-on-close']
            ) ?>
        <? endif ?>
        <? if ($selected_view !== 'admin_info') : ?>
            <?= \Studip\LinkButton::create(
                _('Kontakt'),
                $controller->admin_infoURL($conflict->id),
                ['data-dialog' => 'size=auto;reload-on-close']
            ) ?>
        <? endif ?>
        <? if ($selected_view !== 'conflict') : ?>
            <?= \Studip\LinkButton::create(
                _('Konflikt'),
                $controller->course_conflictURL($conflict->id),
                ['data-dialog' => 'size=auto;reload-on-close']
            ) ?>
        <? endif ?>
    <? endif ?>
</div>
