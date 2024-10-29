<section class="contentbox">
    <header>
        <h1>Sichtbarkeit in Stud.IP</h1>
    </header>
    <section>
        <table width="100%" border="0" cellspacing="10" cellpadding="10">
            <colgroup>
                <col style="width: 47%">
                <col style="width: 6%">
                <col style="width: 47%">
            </colgroup>
            <tr>
                <td colspan="3">
                    <p>Sie haben jetzt die Möglichkeit sich zu entscheiden in Stud.IP sichtbar oder unsichtbar zu sein. Diese Einstellung können jederzeit unter Profil->Einstellungen->Privatsphäre geändert werden.</p>
                    <p>Grundsätzlich und unabhängig davon, ob Sie sichtbar oder unsichtbar sind, gilt:</p>
                    <ul>
                        <li>Sie können Stud.IP aktiv nutzen und sich an Veranstaltungen, Foren etc. beteiligen</li>
                        <li>Teilnehmendenlisten von Veranstaltungen sind nur dann für die Teilnehmenden zugänglich, wenn alle
                            einverstanden sind
                        </li>
                        <li>
                            Sobald Sie im System aktiv werden - d.h. Forumsbeiträge verfassen, sich an nicht-anonymen Umfragen
                            beteiligen, Mails verschicken etc. - wird Ihr Name dabei angegeben und es lässt sich nicht
                            vermeiden, dass andere Nutzerinnen und Nutzer indirekt erkennen können, ob Sie sichtbar oder
                            unsichtbar sind.
                        </li>
                    </ul>
                </td>
            </tr>
            
            <tr>
                <td style="background:#ddffdd; border:1px solid #d0d7e3;" valign="top">
                    <p><b>Wenn Sie sichtbar sind, dann</b></p>
                    <ul>
                        <li>werden Sie in der öffentlichen "Wer-ist-online"-Liste
                            mit Zeitpunkt ihrer letzten Aktivität erscheinen,
                        </li>
                        <li>können Sie alle Kommunikationsmöglichkeiten wie gewohnt
                            nutzen,
                        </li>
                        <li>können Sie in Ihrem Profil trotzdem weitestgehend
                            entscheiden, was andere über Sie erfahren können,
                        </li>
                        <li>können Sie von anderen gefunden und kontaktiert werden,</li>
                        <li>leisten Sie einen Beitrag dazu, Stud.IP weiterhin zu einer aktiven
                            und kommunikativen Plattform zu machen.
                        </li>
                    </ul>
                    <?= \Studip\LinkButton::create('Sichtbar werden', URLHelper::getURL('?vis_state=yes&vis_cmd=apply')) ?>
                </td>
                <td></td>
                <td style="background:#ffdddd; border:1px solid #d0d7e3;" valign="top">
                    <p><b>Wenn Sie unsichtbar sind, dann</b>
                    <ul>
                        <li>können Sie nicht mehr über die Personensuche gefunden werden,</li>
                        <li>werden Sie nicht mehr in der "Wer-ist-online"-Liste erscheinen,</li>
                        <li>können Sie nicht mehr Ihr Profil nutzen,</li>
                        <li>können Sie nicht mehr Ihre E-Mail-Adresse, Ihr Gästebuch, Ihre Stud.IP-Punkte etc. anderen
                            zugänglich machen,
                        </li>
                        <li>können Sie nicht mehr im Adressbuch anderer NutzerInnen stehen.</li>
                    </ul>
                    <?= \Studip\LinkButton::create('Unsichtbar werden', URLHelper::getURL('?vis_state=no&vis_cmd=apply')) ?>
                </td>
            </tr>
            <? if (Config::get()->PRIVACY_URL): ?>
            <tr>
                <td colspan="3">
                    <p>
                        Weitere Informationen entnehme Sie bitte den 
                        <a href="<?=URLHelper::getURL(Config::get()->PRIVACY_URL, ['cancel_login' => 1], true) ?>" target="_bank">Datenschutzerklärungen</a>.
                    </p>
                </td>
            </tr>
            <? endif; ?>
        </table>
    </section>
</section>

