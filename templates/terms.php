<section class="contentbox">
    <header>
        <h1><?= _('Was ist Stud.IP?') ?></h1>
    </header>
    <section>
        <?= _('Stud.IP ist ein Open Source Projekt und steht unter der GNU General Public License (GPL). Das System befindet sich in der ständigen Weiterentwicklung.') ?>

        <? printf(
            _('Für Vorschläge und Kritik findet sich immer ein Ohr. Wenden Sie sich hierzu entweder an die %sStud.IP Crew%s oder direkt an Ihren lokalen %sSupport%s.'),
            '<a href="mailto:studip-users@lists.sourceforge.net">',
            '</a>',
            '<a href="' . URLHelper::getLink('dispatch.php/siteinfo/show') . '">',
            '</a>'
        ) ?>
        <br><br>
        <?= _('Um den vollen Funktionsumfang von Stud.IP nutzen zu können, müssen Sie sich am System anmelden.') ?><br>
        <?= _('Das hat viele Vorzüge:') ?><br>

        <ul>
            <li><?= _('Zugriff auf Ihre Daten von jedem internetfähigen Rechner weltweit,') ?></li>
            <li><?= _('Anzeige neuer Mitteilungen oder Dateien seit Ihrem letzten Besuch,') ?></li>
            <li><?= _('Ein eigenes Profil im System,') ?></li>
            <li><?= _('die Möglichkeit anderen Personen Nachrichten zu schicken oder mit ihnen zu chatten,') ?></li>
            <li><?= _('und vieles mehr.') ?></li>
        </ul>
        <?= _('Mit der Anmeldung werden die nachfolgenden Nutzungsbedingungen akzeptiert:') ?>
    </section>
</section>

<? if ($terms_of_use) : ?>
<section class="contentbox">
    <header>
        <h1><?= _('Nutzungsbedingungen') ?></h1>
    </header>
    <section>
        <? if ($terms_of_use['type'] === 'internal_url') : ?>
            <?= $terms_of_use['content'] ?>
        <? else : ?>
            <strong>
                <a href="<?= URLHelper::getURL($terms_of_use['url']) ?>" title="<?= _('Nutzungsbedingungen') ?>"
                   target="_blank" tabindex="0">
                    <?= URLHelper::getURL($terms_of_use['url']) ?>
                </a>
            </strong>
        <? endif ?>
    </section>
</section>
<? endif ?>
