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
            body {
                background-color: #e7ebf1;
                font-family: 'Lato', Helvetica, Arial, sans-serif;
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
                text-align: center;
                padding-bottom: 15px;
            }

            .studip-mail-header-logo {
                margin-left: auto;
                margin-right: auto;
            }

            .studip-mail-sndrec {
                margin-top: 0;
            }

            .studip-mail-message {
                line-height: 1.33;
                padding: 15px 25px;
            }

            .studip-mail footer {
                border-top: 1px solid #d0d7e3;
                text-align: center;
                padding-top: 15px;
            }
        </style>
    </head>
    <body>
    <article class="studip-mail">
        <header>
            <div class="studip-mail-header-logo" >
                <img alt="" width="130" height="92" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNy4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkViZW5lXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgODQxLjg5IDU5NS4yOCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgODQxLjg5IDU5NS4yOCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8cGF0aCBmaWxsPSIjMTM0MDk0IiBkPSJNNzYzLjE3MiwyNDguMzU1YzAtMTcuNjM5LTE0LjMzMS0zMS45NTYtMzEuOTctMzEuOTU2Yy0xNy42NTIsMC0zMS45NDEsMTQuMzE2LTMxLjk0MSwzMS45NTYNCgkJczE0LjI4OSwzMS45NywzMS45NDEsMzEuOTdDNzQ4Ljg0MSwyODAuMzI1LDc2My4xNzIsMjY1Ljk5NCw3NjMuMTcyLDI0OC4zNTUgTTczMS4yMDIsMTYzLjI3Mg0KCQljLTQ2Ljk1OCwwLTg1LjA3NiwzOC4xMTItODUuMDksODUuMDYzaDI1LjM0YzAuMDA2LTMyLjk2NiwyNi43NjItNTkuNzQzLDU5Ljc1LTU5Ljc0M2MzMywwLDU5Ljc3NywyNi43ODMsNTkuNzc3LDU5Ljc2NA0KCQljMCwzMi45ODUtMjYuNzYyLDU5Ljc1Ni01OS43NSw1OS43NjJ2MjUuMzRjNDYuOTc4LTAuMDEzLDg1LjA2My0zOC4xMzksODUuMDYzLTg1LjEwM1M3NzguMTkzLDE2My4yNzIsNzMxLjIwMiwxNjMuMjcyIi8+DQoJPHJlY3QgeD0iNDg2LjkxNiIgeT0iMzk5LjMxOCIgZmlsbD0iI0FFMEEwRCIgd2lkdGg9IjMwLjY0MSIgaGVpZ2h0PSIzMC42NDEiLz4NCgk8Zz4NCgkJPHBhdGggZmlsbD0iIzNDNDQ0OCIgZD0iTTUxLjAwMiwzODUuNTY3YzEwLjk1MSw5LjUyNCwyNS4yMzYsMTYuMTksMzguMDkzLDE2LjE5YzE0LjUyMiwwLDIxLjY2NC01LjcxNCwyMS42NjQtMTQuOTk4DQoJCQljMC05Ljc2My04LjgwOC0xMi44NTgtMjIuMzgtMTguNTcxbC0xOS45OTgtOC41NzFjLTE2LjE5LTYuNDI5LTMxLjQyNy0xOS43NjEtMzEuNDI3LTQyLjM3OA0KCQkJYzAtMjUuNzEzLDIzLjA5NS00Ni4xODgsNTUuMjM1LTQ2LjE4OGMxNy44NTYsMCwzNi42NjQsNy4xNDIsNDkuOTk2LDIwLjQ3NWwtMTcuNjE3LDIyLjE0MmMtMTAuMjM5LTcuODU4LTIwLTEyLjM4MS0zMi4zOC0xMi4zODENCgkJCWMtMTEuOTA1LDAtMTkuNzYxLDUuMjM5LTE5Ljc2MSwxNC4wNDdjMCw5LjUyNCwxMC4yMzcsMTIuODU2LDIzLjgwOCwxOC4zMzJsMTkuNzYxLDguMDk1DQoJCQljMTguODA4LDcuNjE5LDMwLjcxMiwyMC4yMzcsMzAuNzEyLDQyLjE0MWMwLDI1LjcxMy0yMS40MjcsNDguMDkzLTU4LjMzLDQ4LjA5M2MtMjAuMjM2LDAtNDEuNjY0LTcuNjE5LTU3LjYxNS0yMi4zOA0KCQkJTDUxLjAwMiwzODUuNTY3eiIvPg0KCQk8cGF0aCBmaWxsPSIjM0M0NDQ4IiBkPSJNMTgzLjM3OCwzMzIuMTAzaC0zMi45ODN2LTI3LjYxNWg5OC45NTJ2MjcuNjE1aC0zMi45ODV2OTcuMDM0aC0zMi45ODNWMzMyLjEwM3oiLz4NCgkJPHBhdGggZmlsbD0iIzNDNDQ0OCIgZD0iTTI2MS4xNTUsMzA0LjQ4N2gzMi45ODN2NjguMjY5YzAsMjIuMjQ2LDUuNzU0LDMwLjMsMTguNzk0LDMwLjNjMTMuMDQsMCwxOS4xNzctOC4wNTUsMTkuMTc3LTMwLjMNCgkJCXYtNjguMjY5aDMxLjgzNHY2NC40MzVjMCw0Mi41NzItMTYuMTA5LDYyLjUxNS01MS4wMSw2Mi41MTVzLTUxLjc3Ny0xOS45NDMtNTEuNzc3LTYyLjUxNVYzMDQuNDg3eiIvPg0KCQk8cGF0aCBmaWxsPSIjM0M0NDQ4IiBkPSJNMzc5LjEyMiwzMDQuNDg3aDM2LjgxOWMzNy45NywwLDY0LjA1LDE3LjY0Myw2NC4wNSw2MS43NWMwLDQ0LjEwNS0yNi4wOCw2Mi44OTktNjIuMTMyLDYyLjg5OWgtMzguNzM3DQoJCQlWMzA0LjQ4N3ogTTQxNC4wMjMsNDAyLjY3MmMxOC4wMjcsMCwzMi4yMTgtNy4yODYsMzIuMjE4LTM2LjQzNWMwLTI5LjE0OS0xNC4xOTEtMzUuMjg2LTMyLjIxOC0zNS4yODZoLTEuOTE2djcxLjcyMUg0MTQuMDIzeiIvPg0KCQk8cGF0aCBmaWxsPSIjM0M0NDQ4IiBkPSJNNTMyLjU1LDI3My45MDZoMzUuMjM2djE1NS4yM0g1MzIuNTVWMjczLjkwNnoiLz4NCgkJPHBhdGggZmlsbD0iIzNDNDQ0OCIgZD0iTTU4NC40NDQsMjczLjkwNmg1Ni42NjNjMzMuODA4LDAsNjAuNzEyLDEyLjM4MSw2MC43MTIsNDkuOTk4YzAsMzYuNDI3LTI3Ljg1Niw1Mi42MTUtNjAuNzEyLDUyLjYxNQ0KCQkJaC0yMS40Mjd2NTIuNjE3aC0zNS4yMzVWMjczLjkwNnogTTYzOC45NjQsMzQ4LjY2NWMxOS4yODUsMCwyOC41NjktOC41NzEsMjguNTY5LTI0Ljc2MWMwLTE2LjE5LTkuOTk4LTIyLjE0Mi0yOC41NjktMjIuMTQyDQoJCQloLTE5LjI4NXY0Ni45MDNINjM4Ljk2NHoiLz4NCgk8L2c+DQo8L2c+DQo8L3N2Zz4NCg==">
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
                    <a href="<?= URLHelper::getLink('seminar_main.php', ['again' => 'yes', 'sso' => $sso, 'auswahl' => $data[0]['range_id']]) ?>">
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
