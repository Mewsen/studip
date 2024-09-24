<?php
# Lifter010: TODO
/**
 * @var string $snd_fullname
 * @var string $rec_fullname
 * @var string $rec_username
 * @var string $message
 * @var array $attachments
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
                    <?php if ($snd_fullname) : ?>
                        <?= sprintf(_('%1$s hat Ihnen eine Nachricht in Stud.IP geschickt.'),
                            htmlReady($snd_fullname), htmlReady($rec_fullname), htmlReady($rec_username)) ?>
                    <?php else : ?>
                        <?= sprintf(
                                _('Stud.IP hat eine automatische Systemnachricht für Sie.'),
                                htmlReady($rec_fullname),
                                htmlReady($rec_username)
                        ) ?>
                    <?php endif ?>
                </p>
            </header>
            <section class="studip-mail-message">
                <p>
                  <?= formatReady($message, true, true) ?>
                </p>
                <?php if (isset($attachments) && count($attachments)) : ?>
                <div class="studip-mail-attachments">
                  <?= _('Dateianhänge:') ?>
                    <ul>
                    <?php foreach($attachments as $attachment) : ?>
                      <li>
                        <a href="<?= $attachment->getDownloadURL() ?>"><?= htmlReady($attachment->name .
                                ' (' . relsize($attachment->file->size, false) . ')') ?></a>
                      </li>
                    <?php endforeach ?>
                    </ul>
                 </div>
                <?php endif ?>
            </section>
            <footer>
                <?= sprintf(
                        _('Diese E-Mail ist eine Kopie einer systeminternen Nachricht, die in Stud.IP an %1$s (%2$s) versendet wurde.'),
                        htmlReady($rec_fullname),
                        htmlReady($rec_username)
                    )
                ?>
                <br><?= sprintf(_('Sie erreichen Stud.IP unter %s'),
                    '<a href="' . $GLOBALS['ABSOLUTE_URI_STUDIP'] . '">' . $GLOBALS['ABSOLUTE_URI_STUDIP'] . '</a>') ?>
            </footer>
        </article>
    </body>
</html>
