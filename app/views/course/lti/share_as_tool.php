<section class="contentbox">
    <header><h1><?= _('Status') ?></h1></header>
    <section>
        <p>
            <?= $share_as_tool
                ? _('Die Veranstaltung ist als LTI-Tool freigegeben.')
                : _('Die Veranstaltung ist nicht als LTI-Tool freigegeben.')
            ?>
        </p>
        <? if ($share_as_tool) : ?>
            <form class="default" method="post"
                  action="<?= $controller->link_for('course/lti/save_share_as_tool_settings', ['cid' => $course_id]) ?>">
                <?= CSRFProtection::tokenTag() ?>
                <label>
                    <input type="checkbox" name="share_as_tool" value="0">
                    <?= _('Freigabe als LTI-Tool beenden') ?>
                </label>
                <?= \Studip\Button::create(_('Übernehmen'), 'apply') ?>
            </form>
        <? endif ?>
    </section>
</section>
<? if ($share_as_tool) : ?>
<? else : ?>
    <section class="contentbox">
        <header><h1><?= _('Freigabe als LTI-Tool') ?></h1></header>
        <section>
            <p><?= _('Sie können die Veranstaltung als LTI-Tool freigeben. Hierzu sind folgende Dinge zu beachten:') ?></p>
            <ul>
                <li><?= _('Die Teilnehmendenseite wird aus Datenschutzgründen unsichtbar geschaltet.') ?></li>
                <li><?= _('Über eine angebundene LTI-Plattform können externe Personen auf die Inhalte dieser Veranstaltung zugreifen.') ?></li>
                <li><?= _('Angebundene LTI-Plattformen haben gegebenenfalls nicht das Datenschutzniveau von Stud.IP.') ?></li>
                <li><?= 'Weitere Punkte? TODO' ?></li>
            </ul>
            <form class="default" method="post"
                  action="<?= $controller->link_for('course/lti/save_share_as_tool_settings', ['cid' => $course_id]) ?>">
                <?= CSRFProtection::tokenTag() ?>
                <label>
                    <input type="checkbox" name="share_as_tool" value="1">
                    <?= _('Ich habe die Hinweise zur Kenntnis genommen und möchte die Veranstaltung als LTI-Tool freigeben.') ?>
                </label>
                <?= \Studip\Button::create(_('Übernehmen'), 'apply') ?>
            </form>
        </section>
    </section>
<? endif ?>
