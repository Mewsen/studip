<div class="mpscontainer" data-dialogname="<?= htmlReady($name) ?>">
    <p>
        <div><?= htmlReady($description) ?></div>
        <? foreach($quickfilter as $title => $users) : ?>
            <button class="quickfilter" data-quickfilter="<?= md5($title) ?>"><?= htmlReady($title) ?> (<?= count($users) ?>)</button>
        <? endforeach ?>
    </p>
    <form method="post" class="default" action="<?= URLHelper::getLink('dispatch.php/multipersonsearch/js_form_exec/?name=' . $name); ?>" id="<?= $name ?>" <?= $data_dialog_status ? 'data-dialog' : ''?>
          data-secure="li.ms-selected:gt(0)">
        <?= CSRFProtection::tokenTag() ?>
        <fieldset>
            <select multiple="multiple" id="<?= htmlReady($name . '_selectbox') ?>" name="<?= htmlReady($name . '_selectbox') ?>[]" data-init-js="true">
                <? foreach ($defaultSelectableUsers as $user) : ?>
                    <option value="<?= htmlReady($user->id) ?>"><?= htmlReady($user->getFullName('full_rev_username')) ?> [<?= htmlReady($user->perms) ?>]</option>
                <? endforeach ?>
            </select>
            <?= $additionHTML ?>
        </fieldset>
        <footer data-dialog-button>
            <?= \Studip\Button::create(_('Speichern'), 'confirm', ['data-dialog-button' => true]) ?>
        </footer>
    </form>
</div>
