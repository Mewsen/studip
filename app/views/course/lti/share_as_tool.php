<form class="default" method="post"
      action="<?= $controller->link_for('course/lti/save_share_as_tool_settings', ['cid' => $course_id]) ?>">
    <?= CSRFProtection::tokenTag() ?>
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
                <label>
                    <input type="checkbox" name="share_as_tool" value="0">
                    <?= _('Freigabe als LTI-Tool beenden') ?>
                </label>
                <?= \Studip\Button::create(_('Übernehmen'), 'apply') ?>
            <? endif ?>
        </section>
    </section>
    <? if ($share_as_tool) : ?>
        <section class="contentbox">
            <header><h1><?= _('Einstellungen') ?></h1></header>
            <section>
                <label>
                    <?= _('Einstiegsseite für Personen, die via LTI in die Veranstaltung wechseln:') ?>
                    <select name="lti_entry_point">
                        <option value="" <?= empty($lti_entry_point) ? 'selected' : '' ?>>
                            <?= _('Keine besondere Einstiegsseite') ?>
                        </option>
                        <!-- TODO: more options -->
                    </select>
                </label>
            </section>
        </section>
        <section class="contentbox">
            <header><h1><?= _('Angebundene LTI-Plattformen') ?></h1></header>
            <section>
                TODO
            </section>
        </section>
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
                <label>
                    <input type="checkbox" name="share_as_tool" value="1">
                    <?= _('Ich habe die Hinweise zur Kenntnis genommen und möchte die Veranstaltung als LTI-Tool freigeben.') ?>
                </label>
                <?= \Studip\Button::create(_('Übernehmen'), 'apply') ?>
            </section>
        </section>
    <? endif ?>
</form>
