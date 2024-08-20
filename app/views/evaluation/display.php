<? if ($admin || $evaluations): ?>
<article class="studip">
    <header>
        <h1>
            <?= Icon::create('vote', 'info')->asImg(); ?>
            <?= _('Evaluationen') ?>
        </h1>
        <nav>
        <? if ($admin): ?>
            <a href="<?= URLHelper::getLink('admin_evaluation.php', ['rangeID' => $range_id]) ?>">
                <?= Icon::create('edit', 'clickable')->asImg(
                    [
                        'title'      => _('Bearbeiten'),
                        'aria-label' => _('Bearbeiten')
                    ]
                ) ?>
            </a>
        <? endif; ?>
        </nav>
    </header>

    <? if (!$evaluations): ?>
        <section>
            <?= _('Es sind keine Evaluationen vorhanden. Um eine neue Evaluation zu erstellen, können Sie die Aktion "Bearbeiten" nutzen.') ?>
        </section>
    <? else: ?>
        <? foreach ($evaluations as $evaluation): ?>
            <?= $this->render_partial('evaluation/_evaluation.php', ['evaluation' => $evaluation]); ?>
        <? endforeach; ?>
    <? endif; ?>
</article>
<? endif; ?>
