<?php
/**
 * @var Course $studygroup
 * @var array $sidebarActions
 */
 ?>
<article class="studip">
    <header>
        <h1><?= _('Grunddaten') ?></h1>
    </header>
    <section>
        <dl style="margin: 0">
            <dt><?= _('Name der Studiengruppe') ?></dt>
            <dd><?= htmlReady($studygroup->name) ?></dd>
            <? if ((string) $studygroup->Beschreibung): ?>
                <dt><?= _('Beschreibung') ?></dt>
                <dd><?= formatLinks($studygroup->Beschreibung) ?></dd>
            <? endif; ?>

        <? if ((string) $studygroup->beschreibung): ?>
            <dt><?= _('Beschreibung') ?></dt>
            <dd><?= formatLinks($studygroup->beschreibung) ?></dd>
        <? endif; ?>
            <dt><?= _('Moderiert von') ?></dt>
            <dd>
                <ul class="list-csv">
                <? foreach ($studygroup->getMembersWithStatus(['dozent', 'tutor']) as $mod) : ?>
                    <li>
                        <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $mod->username]) ?>">
                            <?= htmlReady($mod->user->getFullName()) ?>
                        </a>
                    </li>
                <? endforeach ?>
                </ul>
            </dd>
        </dl>
    </section>
</article>

<? if (count($studygroup->tags) > 0) : ?>
<article class="studip">
    <header>
        <h1><?= _('Schlagwörter') ?></h1>
    </header>
    <section>
        <? foreach ($studygroup->tags as $tag) : ?>
            <a href="<?= URLHelper::getLink('dispatch.php/studygroup/browse', ['q' => $tag['name']]) ?>">
                <?= htmlReady('#'.$tag['name']) ?>
            </a>
        <? endforeach ?>
    </section>
</article>
<? endif ?>

<div class="hidden-medium-up">
<? foreach ($sidebarActions as $action) : ?>
    <?= Studip\LinkButton::create($action->label, $action->url) ?>
<? endforeach ?>
</div>
