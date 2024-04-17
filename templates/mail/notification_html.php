<?
# Lifter010: TODO
/**
 * @var string $rec_fullname
 * @var string $rec_username
 * @var array $news
 * @var string $sso
 */
?>
<html>
    <head>
        <style>
            html {
                background-color: #e7ebf1;
                font-family: 'Lato', Helvetica, Arial, sans-serif;
                height: 100%;
                width: 100%;
            }

            a, a:link, a:visited {
                color: #28497c;
                text-decoration: none;
            }
            a[href] {
                transition: color .3s;
            }
            a[disabled] {
                pointer-events: none;
            }

            a:hover, a:active {
                color: #d60000;
                text-decoration: none;
            }

            .studip-mail {
                background-color: #ffffff;
                border: 1px solid #d0d7e3;
                margin: 25px auto;
                padding: 10px 25px 25px 25px;
                width: 700px;
            }

            .studip-mail header {
                border-bottom: 1px solid #d0d7e3;
                display: block;
                text-align: center;
                padding-bottom: 15px;
            }

            .studip-mail header .studip-mail-header-logo {
                margin-left: auto;
                margin-right: auto;
                width: 100%;
            }

            .studip-mail header .studip-mail-sndrec {
                margin-left: auto;
                margin-right: auto;
                margin-top: 0;
                width: 100%;
            }

            .studip-mail .studip-mail-message {
                line-height: 1.33;
                padding: 15px 25px;
            }

            .studip-mail footer {
                border-top: 1px solid #d0d7e3;
                text-align: center;
                margin-left: auto;
                margin-right: auto;
                padding-top: 15px;
                width: 100%;
            }
        </style>
    </head>
    <body>
    <article class="studip-mail">
        <header>
            <div class="studip-mail-header-logo" >
                <img alt="" width="130" height="92" src="cid:studiplogo">
            </div>
            <p class="studip-mail-sndrec">
                <?= sprintf(
                    _('Stud.IP hat eine automatische Systemnachricht für Sie.'),
                    htmlReady($rec_fullname),
                    htmlReady($rec_username)
                ) ?>
            </p>
            <p>
                <?= _("Sie erhalten hiermit in regelmäßigen Abständen Informationen über Neuigkeiten und Änderungen in belegten Veranstaltungen.") ?>
                <br><br>
                <?= _("Über welche Inhalte und in welchem Format Sie informiert werden wollen, können Sie hier einstellen:") ?>
                <br>
                <a href="<?= URLHelper::getLink('dispatch.php/settings/notification', ['again' => 'yes', 'sso' => $sso]) ?>">
                    <?= URLHelper::getLink('dispatch.php/settings/notification', ['again' => 'yes', 'sso' => $sso]) ?>
                </a>
            </p>
        </header>
        <section class="studip-mail-message">
            <table class="default">
              <? foreach ($news as $sem_titel => $data) : ?>
                <tr class="table_header_bold">
                  <td style="font-weight: bold;">
                    <a href="<?= URLHelper::getLink('seminar_main.php', ['again' => 'yes', 'sso' => $sso, 'auswahl' => $data[0]['seminar_id']]) ?>">
                      <?= htmlReady($sem_titel) ?>
                      <?= (($semester = Course::find($data[0]['range_id'])->semester_text) ? ' ('.$semester.')' : '') ?>
                    </a>
                  </td>
                </tr>
                <? foreach ($data as $module) : ?>
                <tr class="<?= TextHelper::cycle('hover_odd', 'hover_even') ?>">
                  <td>
                    <a href="<?= URLHelper::getLink($module['url'], ['sso' => $sso]) ?>"><?= htmlReady($module['text']) ?></a>
                  </td>
                </tr>
                <? endforeach ?>
              <? endforeach ?>
            </table>
        </section>
        <footer>
            <?= sprintf(
                _('Diese E-Mail wurde von Stud.IP an %1$s (%2$s) versendet.'),
                htmlReady($rec_fullname),
                htmlReady($rec_username)
            )
            ?>
        </footer>
    </body>
</html>
