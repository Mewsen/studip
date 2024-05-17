<article class="studip">
    <header>
        <h1>
            <?= _('Veranstaltungen') ?>
        </h1>
    </header>

    <section>
    <? foreach ($seminare as $semester => $seminar) : ?>
        <b><?= htmlReady($semester) ?></b><br>
        <ul class="clean">
        <? foreach ($seminar as $one) : ?>
            <li>
                <a href="<?= URLHelper::getScriptLink('dispatch.php/course/details', ['sem_id' => $one->id])?>">
                    <?= htmlReady($one->getFullName('number-name')) ?>
                    <? if ($one->start_semester !== $one->end_semester) : ?>
                        (<?= htmlReady($one->getFullName('sem-duration-name')) ?>)
                    <? endif ?>
                </a>
            </li>
        <?endforeach?>
        </ul>
    <?endforeach?>
    </section>
</article>
