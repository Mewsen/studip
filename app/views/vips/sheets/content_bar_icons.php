<? if (isset($prev_exercise_url)): ?>
    <a href="<?= htmlReady($prev_exercise_url) ?>">
        <?= Icon::create('arr_1left')->asImg(24, ['title' => _('Vorige Aufgabe')]) ?>
    </a>
<? else: ?>
    <span>
        <?= Icon::create('arr_1left', Icon::ROLE_INACTIVE)->asImg(24) ?>
    </span>
<? endif ?>

<? if (isset($next_exercise_url)): ?>
    <a href="<?= htmlReady($next_exercise_url) ?>">
        <?= Icon::create('arr_1right')->asImg(24, ['title' => _('Nächste Aufgabe')]) ?>
    </a>
<? else: ?>
    <span>
        <?= Icon::create('arr_1right', Icon::ROLE_INACTIVE)->asImg(24) ?>
    </span>
<? endif ?>
