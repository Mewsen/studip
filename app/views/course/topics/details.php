<div>
    <h2><?= htmlReady($topic->title) ?></h2>
    <? $has_content = false ?>
    <? if (
        ($documents_activated && $topic->folders->first())
        || ($forum_activated && $topic->forum_thread_url)
    ) : ?>
        <h3><?= _('Materialien') ?></h3>
        <?= $this->render_partial('course/topics/_material.php', ['always_show' => true]) ?>
        <? $has_content = true ?>
    <? endif ?>

    <? if (count($topic->dates) > 0) : ?>
        <h3><?= _('Termine') ?></h3>
        <?= $this->render_partial('course/topics/_dates.php') ?>
        <? $has_content = true ?>
    <? endif ?>

    <? if ($topic->description) : ?>
        <h3><?= _('Beschreibung') ?></h3>
        <?= formatReady($topic->description) ?>
        <? $has_content = true ?>
    <? endif ?>

    <? if (!$has_content) : ?>
        <?= _('Keine weiteren Informationen verfügbar.') ?>
    <? endif ?>
</div>
