<? if ($admin || $termine): ?>
<article class="studip">
    <header>
        <h1>
            <?= Icon::create('schedule', 'info')->asImg() ?>
            <?= htmlReady($title) ?>
        </h1>
        <nav>
    <? if ($admin): ?>
        <? if ($isProfile): ?>
        <a href="<?= URLHelper::getLink('dispatch.php/calendar/single/edit/', ['source_page' => 'dispatch.php/profile']) ?>"
           title="<?= _('Neuen Termin anlegen') ?>" aria-label="<?= _('Neuen Termin anlegen') ?>">
            <?= Icon::create('add', 'clickable')->asImg(['class' => 'text-bottom']) ?>
        </a>
        <? else: ?>
        <a href="<?= URLHelper::getLink("dispatch.php/course/timesrooms", ['cid' => $range_id]) ?>"
           title="<?= _('Neuen Termin anlegen') ?>" aria-label="<?= _('Neuen Termin anlegen') ?>">
            <?= Icon::create('admin', 'clickable')->asImg(['class' => 'text-bottom']) ?>
        </a>
        <? endif; ?>
    <? endif; ?>
        </nav>
    </header>
  <? if ($termine): ?>
    <? foreach ($termine as $termin): ?>
        <?= $this->render_partial('calendar/contentbox/_termin.php', ['termin' => $termin]); ?>
    <? endforeach; ?>
<? else: ?>
    <section>
    <? if ($isProfile): ?>
        <?= _('Es sind keine aktuellen Termine vorhanden. Um neue Termine zu erstellen, können Sie die Aktion "Neuen Termin anlegen" benutzen.') ?>
    <? else: ?>
        <?= _('Es sind keine aktuellen Termine vorhanden. Um neue Termine zu erstellen, können Sie die Aktion "Neuen Termin anlegen" benutzen.') ?>
    <? endif; ?>
    </section>
  <? endif; ?>
</article>
<? endif; ?>
