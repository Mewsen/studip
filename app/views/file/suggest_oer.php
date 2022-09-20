<form action='<?= $controller->url_for('file/suggest_oer/' . $file_ref_id)?>'
      class='default' method='POST' data-dialog="reload-on-close">
    <?= CSRFProtection::tokenTag() ?>

    <div id="oer_suggestion">
        <span><?= _('Das Material gefällt Ihnen?') ?></span>
        <br/>
        <span><?= _('Schlagen Sie es zum Teilen im OER Campus vor.') ?></span>
    </div>
    <p><?= _('Schreiben Sie der Autorin/dem Autoren:') ?></p>
    <label for="additional_text">
        <textarea   class="add_toolbar wysiwyg"
                    name  = "additional_text"
                    id    = "additional_text"
                    rows  = "3"
        ></textarea>
    </label>
    <p><?= _('Eine Nachricht ist freiwillig. Ihr Vorschlag wird anonym versendet.') ?></p>

<div class="oercampus_editmaterial">
    <div class="hgroup" id="oer_suggestion_hgroup">

        <label for="oer_logo_uploader">
            <article class="contentbox" title="<?= htmlReady($file->getFilename()) ?>">
                <header>
                    <h1>
                        <studip-icon shape="file"
                                     role="clickable"
                                     size="20"
                                     class="text-bottom"></studip-icon>
                        <div class="title"><?= htmlReady($file->getFilename()) ?></div>
                    </h1>
                </header>
                <div class="image"
                     :style=""></div>
            </article>
        </label>

        <!-- file information on the right side -->
        <aside id="file_aside">
            <table class="default nohover" id="oer_suggestion_table">
                <caption><?= htmlReady($file->getFilename()) ?></caption>
                <tbody>
                <tr>
                    <td><?= _('Größe') ?></td>
                    <? $size = $file->getSize() ?>
                    <td><?= $size !== null ? relSize($file->getSize(), false) : "-" ?></td>
                </tr>
                <tr>
                    <td><?= _('Downloads') ?></td>
                    <td><?= htmlReady($file->getDownloads()) ?></td>
                </tr>
                <tr>
                    <td><?= _('Erstellt') ?></td>
                    <td><?= date('d.m.Y H:i', $file->getMakeDate()) ?></td>
                </tr>
                <tr>
                    <td><?= _('Geändert') ?></td>
                    <td><?= date('d.m.Y H:i', $file->getLastChangeDate()) ?></td>
                </tr>
                <tr>
                    <td><?= _('Besitzer/-in') ?></td>
                    <td>
                        <? $user_id = $file->getUserId() ?>
                        <? if ($user_id) : ?>
                            <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => get_username($user_id)]) ?>">
                                <?= htmlReady($file->getUserName()) ?>
                            </a>
                        <? else : ?>
                            <?= htmlReady($file->getUserName()) ?>
                        <? endif ?>
                    </td>
                </tr>

                </tbody>
            </table>
        </aside>
    </div>
</div>



    <footer data-dialog-button>
        <?= Studip\Button::create(_("Teilen vorschlagen"))?>
    </footer>
</form>
