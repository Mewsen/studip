<div id="file_suggest_oer">
    <form action='<?= $controller->url_for('file/suggest_oer/' . $file_ref_id)?>'
          class='default' method='POST' data-dialog="reload-on-close">
        <?= CSRFProtection::tokenTag() ?>
        <p class="suggestion_text"><?= _('Das Material gefällt Ihnen?') ?></p>
        <p class="suggestion_text"><?= sprintf(_('Das Material gefällt Ihnen? Schlagen Sie es zum Teilen im %s vor.'), Config::get()->OER_TITLE) ?></p>

        <p><?= _('Schreiben Sie der Autorin/dem Autoren:') ?></p>

        <label for="additional_text">

            <textarea   class = "add_toolbar wysiwyg"
                        name  = "additional_text"
                        id    = "additional_text"
                        rows  = "3"
                        placeholder = "<?= _("Warum gefällt Ihnen das Material?") ?>"
            ></textarea>
        </label>
        <span class="">
                <?= _("Eine Nachricht ist freiwillig. Ihr Vorschlag wird anonym versendet.") ?>
            </span>
        <footer data-dialog-button>
            <?= Studip\Button::create(_("Teilen vorschlagen"))?>
        </footer>
    </form>
</div>

<div id="oer_file_details">

    <div id="preview_container" class="oercampus_editmaterial">
        <div class="hgroup">
            <label for="oer_logo_uploader">
                <article class="contentbox" title="">
                    <header>
                        <h1>
                            <studip-icon shape="file"
                                         role="clickable"
                                         size="20"
                                         class="text-bottom">

                            </studip-icon>
                            lorem ipsum
                        </h1>
                    </header>
                    <div class="image"
                         :style="'background-image: url(' + logo_url + ');' + (!customlogo ? ' background-size: 60% auto;': '')"></div>
                </article>
            </label>

        </div>
    </div>

    <aside id="oer_aside">
        <table class="default nohover">
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


