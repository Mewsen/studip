<? foreach(PageLayout::getMessages() as $pm) : ?>
    <?= $pm ?>
<? endforeach; ?>
<form class="default" action="<?= $controller->link_for() ?>" method="post">
    <?= CSRFProtection::tokenTag()?>

    <input type="hidden" name="type" value="<?=htmlReady($type)?>">
    <input type="hidden" name="rule_id" value="<?=htmlReady($rule_id)?>">
    <fieldset>
        <legend><?= _('Neue Anmelderegel erstellen') ?></legend>
        <? foreach ($types as $type) : ?>
            <admission-rule-config type="<?= htmlReady($type) ?>"></admission-rule-config>
        <? endforeach ?>
        <label class="caption"><?= _("Name für diese Anmelderegel")?></label>
        <input type="text" name="instant_course_set_name" size="70" value="<?= htmlReady($course_set_name) ?>">
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::create(_("Speichern"), 'save', ['data-dialog' => ''])?>
    </footer>
</form>
