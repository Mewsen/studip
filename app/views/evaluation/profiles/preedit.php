<?php
/**
 * @var Evaluation_ProfilesController $controller
 */
?>

<?php if ($controller->total_sem_count > count($controller->semesters)) :?>
    <form method="post">
        <?= CSRFProtection::tokenTag() ?>
        <h2><?= _('Neues Profil anlegen') ?></h2>
        <div class="clickable">
            <button
                class="as-link"
                formaction="<?= $controller->link_for('/edit') ?>"
                data-dialog="size=default"
                style="vertical-align: center"
            >
                <?= Icon::create('add')->asSvg(50) ?>
                <p><?= _('Neu anlegen') ?></p>
            </button>
        </div>
    </form>

    <?php if (count($controller->semesters)) : ?>
        <form method="post">
            <?= CSRFProtection::tokenTag() ?>
            <h2><?= _('Profildaten übernehmen') ?></h2>
            <label>
                <?= _('Aus Profil von Semester') ?><br/>
                <select name="sem_select">
                    <?php foreach ($controller->semesters as $key => $semester) : ?>
                        <option value="<?= $key ?>"><?= htmlReady($semester) ?></option>
                    <?php endforeach ?>
                </select>
            </label>
            <br/>
            <br/>
            <div class="clickable">
                <button
                    class="as-link"
                    formaction="<?= $controller->link_for('/edit') ?>"
                    data-dialog="size=default"
                >
                    <?= Icon::create('copy')->asSvg(50) ?>
                    <p><?= _('Daten übernehmen') ?></p>
                </button>
            </div>
        </form>
    <?php endif ?>
<?php else : ?>
    <br/>
    <p><?= _('Es ist kein Semester ohne Profil vorhanden.') ?></p>
<?php endif ?>
