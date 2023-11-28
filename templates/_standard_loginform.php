<?php

use Studip\Button;

?>
<form class="default" name="login_form" id="login_form" method="post" action="<?= URLHelper::getLink(Request::url(), ['cancel_login' => NULL]) ?>" style="display: <?= $display ?>">

    <section>
        <label>
            <span class="required"><?= _('Benutzername') ?></span>
            <? if (Config::get()->USERNAME_TOOLTIP_ACTIVATED) : ?>
                <?= tooltipIcon(htmlReady((string)Config::get()->USERNAME_TOOLTIP_TEXT)) ?>
            <? endif ?>
            <input type="text" <?= mb_strlen($uname) ? '' : 'autofocus' ?>
                   id="loginname"
                   name="loginname"
                   value="<?= htmlReady($uname) ?>"
                   size="20"
                   autocorrect="off"
                   autocapitalize="off"
                   title="<?= _('Der Benutzername entspricht nicht den Anforderungen') ?>"
                   required>
        </label>
    </section>
    <p id="loginname_caps" style="display: none"><?= _('Feststelltaste ist aktiviert!') ?></p>
    <section>
        <label for="password" style="position: relative">
            <span class="required"><?= _('Passwort') ?></span>
            <? if (Config::get()->PASSWORD_TOOLTIP_ACTIVATED) : ?>
                <?= tooltipIcon(htmlReady((string)Config::get()->PASSWORD_TOOLTIP_TEXT)) ?>
            <? endif ?>
            <input type="password" <?= mb_strlen($uname) ? 'autofocus' : '' ?>
                   id="password"
                   name="password"
                   size="20"
                   required>

            <i id="password-toggle" href=""
                <?= tooltip(_('Passwort zeigen/verstecken'), true) ?>>
                <?= Icon::create('visibility-checked')->asImg(20, ['id' => 'visible-password']) ?>
                <?= Icon::create('visibility-invisible')->asImg(20, ['id' => 'invisible-password']) ?>
            </i>

        </label>
    </section>
    <p id="password_caps" style="display: none"><?= _('Feststelltaste ist aktiviert!') ?></p>
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="login_ticket" value="<?=Seminar_Session::get_ticket();?>">
    <input type="hidden" name="resolution"  value="">
    <input type="hidden" name="device_pixel_ratio" value="1">

    <div style="text-align: right; width: 95%">
        <? if (Config::get()->ENABLE_REQUEST_NEW_PASSWORD_BY_USER && in_array('Standard', $GLOBALS['STUDIP_AUTH_PLUGIN'])): ?>
        <a href="<?= URLHelper::getLink('dispatch.php/new_password?cancel_login=1') ?>">
            <? else: ?>
            <a href="mailto:<?= $GLOBALS['UNI_CONTACT'] ?>?subject=<?= rawurlencode('Stud.IP Passwort vergessen - '.Config::get()->UNI_NAME_CLEAN) ?>&amp;body=<?= rawurlencode('Ich habe mein Passwort vergessen. Bitte senden Sie mir ein Neues.\nMein Nutzername: ' . htmlReady($uname) . "\n") ?>">
                <? endif; ?>
                <?= _('Passwort vergessen?') ?>
            </a>
    </div>
    <?= Button::createAccept(_('Anmelden'), _('Login'), ['id' => 'submit_login']); ?>


</form>
