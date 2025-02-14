<?php

use Studip\Button;

/**
 * @var bool $hidden
 * @var string $uname;
 */
$username_tooltip_text = (string) Config::get()->USERNAME_TOOLTIP_TEXT;
$password_tooltip_text = (string) Config::get()->PASSWORD_TOOLTIP_TEXT;
?>

<form class="default <?= $hidden ? 'hide' : '' ?> <?= $login_form_class ?>" name="login_form" id="login-form" method="post"
    action="<?= URLHelper::getLink(Request::url(), ['cancel_login' => null]) ?>">
    <section>
        <? $withTooltip = $username_tooltip_text !== '' || $password_tooltip_text !== ''; ?>
        <label class="<?= $withTooltip ? 'with-tooltip' :  ''?>">
            <span class="sr-only"><?= _('Benutzername') ?></span>
            <input type="text" <?= (mb_strlen($uname ?? '') || $hidden) ? '' : 'autofocus' ?> id="loginname"
                name="loginname" value="<?= htmlReady($uname ?? '') ?>" size="20" spellcheck="false"
                autocapitalize="off" autocomplete="username" placeholder="<?= _('Benutzername') ?>" required>
        </label>
        <? if ($username_tooltip_text): ?>
            <?= tooltipIcon($username_tooltip_text) ?>
        <? endif ?>
        <label class="<?= $withTooltip ? 'with-tooltip' :  ''?>" style="position: relative">
            <span class="sr-only"><?= _('Passwort') ?></span>

            <input type="password" <?= mb_strlen($uname ?? '') && !$hidden ? 'autofocus' : '' ?> id="password"
                   class="allow-plaintext-toggle"
                   name="password"
                   autocomplete="current-password"
                   size="20"
                   required
                   placeholder="<?= _('Passwort') ?>"
            >
        </label>
        <? if ($password_tooltip_text): ?>
            <?= tooltipIcon($password_tooltip_text) ?>
        <? endif ?>
        <p id="password-caps" style="display: none"><?= _('Feststelltaste ist aktiviert!') ?></p>
    </section>
    <? if (Config::get()->ENABLE_REQUEST_NEW_PASSWORD_BY_USER && in_array('Standard', $GLOBALS['STUDIP_AUTH_PLUGIN'])): ?>
        <a style="line-height: 1 !important"
            href="<?= URLHelper::getLink('dispatch.php/new_password', ['cancel_login' => 1]) ?>">
    <? else: ?>
        <a style="line-height: 1 !important"
            href="mailto:<?= $GLOBALS['UNI_CONTACT'] ?>?subject=<?= rawurlencode('Stud.IP Passwort vergessen - ' . Config::get()->UNI_NAME_CLEAN) ?>&amp;body=<?= rawurlencode('Ich habe mein Passwort vergessen. Bitte senden Sie mir ein Neues.\nMein Nutzername: ' . htmlReady($uname) . "\n") ?>">
    <? endif; ?>
            <?= _('Passwort vergessen?') ?>
        </a>

    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="login_ticket" value="<?= htmlReady(get_ticket()) ?>">
    <input type="hidden" name="resolution" value="">

    <div class="login-button-wrapper">
        <?= Button::create(_('Anmelden'), _('Login'), ['id' => 'submit_login']); ?>
    </div>
</form>
