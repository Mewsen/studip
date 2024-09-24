<form action="" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <div class="file_select_possibilities">
        <div>
            <div class="clickable">
                <?= Icon::create('archive2')->asInput(50, [
                    'formaction' => $controller->deleteversionURL($page, ['redirect_to' => 'page']),
                    'data-confirm' => _('Wirklich die letzte Änderung löschen?')
                ]) ?>
                <button
                    class="undecorated"
                    data-confirm="<?= _('Wirklich die letzte Änderung löschen?') ?>"
                    formaction="<?= $controller->deleteversionURL($page, ['redirect_to' => 'page']) ?>">
                    <?= _('Nur die letzte Änderung löschen') ?>
                </button>
            </div>
            <div class="clickable">
                <?= Icon::create('wiki')->asInput(50, [
                    'formaction' => $controller->deleteURL($page),
                    'data-confirm' => _('Wollen Sie wirklich die komplette Seite löschen?')
                ]) ?>
                <button
                    class="undecorated"
                    data-confirm="<?= _('Wollen Sie wirklich die komplette Seite löschen?') ?>"
                    formaction="<?= $controller->deleteURL($page) ?>">
                    <?= _('Ganze Wikiseite löschen') ?>
                </button>
            </div>
        </div>
    </div>
</form>
