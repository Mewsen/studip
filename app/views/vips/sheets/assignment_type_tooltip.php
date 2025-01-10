<dl style="margin-top: 0;">
    <dt><?= _('Übung') ?></dt>
    <dd>
        <?= _('Hausaufgabe, freie Bearbeitung im festgelegten Zeitraum, auch Gruppenarbeit möglich') ?>
    </dd>
    <dt><?= _('Selbsttest') ?></dt>
    <dd>
        <?= _('Kontrolle des Lernfortschritts, Feedback nach der Abgabe einer Lösung, automatische Korrektur') ?>
    </dd>
    <dt><?= _('Klausur') ?></dt>
    <dd>
        <?= _('Online-Klausur mit individueller Bearbeitungszeit, konfigurierbare Zugangsbeschränkungen') ?>
    </dd>
</dl>

<a href="<?= format_help_url(PageLayout::getHelpKeyword()) ?>" target="_blank">
    <?= _('Weitere Informationen in der Hilfe') ?>
</a>
