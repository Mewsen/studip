<?php
/**
 * @var SimpleCollection $all_lock_rules
 */
?>
<label><?= _('Für alle Veranstaltungen') ?>
    <select name="lock_sem_all" style="max-width: 200px">
        <? foreach ($all_lock_rules as $lock_rule): ?>
            <option value="<?= htmlReady($lock_rule->id) ?>">
                <?= htmlReady($lock_rule->name) ?>
            </option>
        <? endforeach ?>
    </select>
</label>

<?= \Studip\Button::createAccept(_('Zuweisen'), 'all', ['formaction' => URLHelper::getURL('dispatch.php/admin/courses/set_lockrule')]); ?>
