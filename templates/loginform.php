<?php
# Lifter010: TODO
use Studip\Button, Studip\LinkButton;

// Get background images (this should be resolved differently since mobile
// browsers might still download the desktop background)
if (!match_route('web_migrate.php')) {
    $bg_desktop = LoginBackground::getRandomPicture('desktop');
    if ($bg_desktop) {
        $bg_desktop = $bg_desktop->getURL();
    } else {
        $bg_desktop = URLHelper::getURL('pictures/loginbackgrounds/1.jpg');
    }
    $bg_mobile = LoginBackground::getRandomPicture('mobile');
    if ($bg_mobile) {
        $bg_mobile = $bg_mobile->getURL();
    } else {
        $bg_mobile = URLHelper::getURL('pictures/loginbackgrounds/2.jpg');
    }
} else {
    $bg_desktop = URLHelper::getURL('pictures/loginbackgrounds/1.jpg');
    $bg_mobile = URLHelper::getURL('pictures/loginbackgrounds/2.jpg');
}
?>
<main id="content">
    <div id="background-desktop" style="background: url(<?= $bg_desktop ?>) no-repeat top left/cover;"></div>
    <div id="background-mobile" style="background: url(<?= $bg_mobile ?>) no-repeat top left/cover;"></div>
    <? if ($loginerror): ?>
        <!-- failed login code -->
        <?= MessageBox::error(_('Bei der Anmeldung trat ein Fehler auf!'), [
            $error_msg,
            sprintf(
                _('Bitte wenden Sie sich bei Problemen an: <a href="mailto:%1$s">%1$s</a>'),
                $GLOBALS['UNI_CONTACT']
            )
        ]) ?>
    <? endif; ?>

    <?= implode('', PageLayout::getMessages()); ?>

    <div id="loginbox">
        <form class="default" name="login" method="post" action="<?= URLHelper::getLink(Request::url(), ['cancel_login' => NULL]) ?>">
            <header>
                <h1 style="margin: 0; padding-bottom:10px;">
                    <?=_('Herzlich willkommen!')?>
                </h1>
            </header>
            <section>
                <label>
                    <?= _('Benutzername:') ?>
                    <input type="text" <?= mb_strlen($uname) ? '' : 'autofocus' ?>
                           id="loginname" name="loginname"
                           value="<?= htmlReady($uname) ?>"
                           size="20"
                           autocorrect="off" autocapitalize="off">
                    <? if (Config::get()->USERNAME_TOOLTIP_ACTIVATED) : ?>
                        <?= tooltipIcon(htmlReady((string)Config::get()->USERNAME_TOOLTIP_TEXT)) ?>
                    <? endif ?>
                </label>
            </section>
            <p id="loginname_caps" style="display: none"><?= _('Feststelltaste ist aktiviert!') ?></p>
            <section>
                <label for="password" style="position: relative">
                    <?= _('Passwort:') ?>
                    <input type="password" <?= mb_strlen($uname) ? 'autofocus' : '' ?>
                           id="password" name="password" size="20">

                    <i id="password_toggle" style="position: absolute;right: 30px;bottom: 0px; cursor: pointer;" href=""
                        <?= tooltip(_('Passwort zeigen/verstecken'), true) ?>>
                        <?= Icon::create('visibility-checked')->asImg(20) ?>
                    </i>

                    <? if (Config::get()->PASSWORD_TOOLTIP_ACTIVATED) : ?>
                        <?= tooltipIcon(htmlReady((string)Config::get()->PASSWORD_TOOLTIP_TEXT)) ?>
                    <? endif ?>
                </label>
            </section>
            <p id="password_caps" style="display: none"><?= _('Feststelltaste ist aktiviert!') ?></p>
            <?= CSRFProtection::tokenTag() ?>
            <input type="hidden" name="login_ticket" value="<?=Seminar_Session::get_ticket();?>">
            <input type="hidden" name="resolution"  value="">
            <input type="hidden" name="device_pixel_ratio" value="1">
            <?= Button::createAccept(_('Anmelden'), _('Login')); ?>
        </form>

        <div>
            <? if (Config::get()->ENABLE_REQUEST_NEW_PASSWORD_BY_USER && in_array('Standard', $GLOBALS['STUDIP_AUTH_PLUGIN'])): ?>
                <a href="<?= URLHelper::getLink('dispatch.php/new_password?cancel_login=1') ?>">
            <? else: ?>
                <a href="mailto:<?= $GLOBALS['UNI_CONTACT'] ?>?subject=<?= rawurlencode('Stud.IP Passwort vergessen - '.Config::get()->UNI_NAME_CLEAN) ?>&amp;body=<?= rawurlencode('Ich habe mein Passwort vergessen. Bitte senden Sie mir ein Neues.\nMein Nutzername: ' . htmlReady($uname) . "\n") ?>">
            <? endif; ?>
                    <?= _('Passwort vergessen') ?>
                </a>

        </div>

        <header>
            <h1><?= htmlReady(Config::get()->UNI_NAME_CLEAN) ?></h1>
        </header>
        <nav>
            <ul>
                <? foreach (Navigation::getItem('/login') as $key => $nav) : ?>
                    <? if ($nav->isVisible()) : ?>
                        <? $name_and_title = explode(' - ', $nav->getTitle()) ?>
                        <li class="login_link">
                            <? if (is_internal_url($url = $nav->getURL())) : ?>
                            <? SkipLinks::addLink($name_and_title[0], $url) ?>
                            <a href="<?= URLHelper::getLink($url) ?>?cancel_login=1">
                                <? else : ?>
                                <a href="<?= htmlReady($url) ?>" target="_blank" rel="noopener noreferrer">
                                    <? endif ?>
                                    <?= htmlReady($name_and_title[0]) ?>
                                    <p>
                                        <?= htmlReady(!empty($name_and_title[1]) ? $name_and_title[1] : $nav->getDescription()) ?>
                                    </p>
                                </a>
                        </li>
                    <? endif ?>
                <? endforeach ?>
            </ul>
        </nav>
        <footer>
            <? if ($GLOBALS['UNI_LOGIN_ADD']) : ?>
                <div class="uni_login_add">
                    <?= $GLOBALS['UNI_LOGIN_ADD'] ?>
                </div>
            <? endif; ?>

            <div id="languages">
                <? foreach ($GLOBALS['INSTALLED_LANGUAGES'] as $temp_language_key => $temp_language): ?>
                    <?= Assets::img('languages/' . $temp_language['picture'], ['alt' => $temp_language['name'], 'size' => '24']) ?>
                    <a href="index.php?set_language=<?= $temp_language_key ?>">
                        <?= htmlReady($temp_language['name']) ?>
                    </a>
                <? endforeach; ?>
            </div>

            <div id="contrast">
                <? if (isset($_SESSION['contrast'])) : ?>
                    <?= Icon::create('accessibility')->asImg(24) ?>
                    <a href="index.php?unset_contrast=1"><?= _('Normalen Kontrast aktivieren') ?></a>
                    <?= tooltipIcon(_('Aktiviert standardmäßige, nicht barrierefreie Kontraste.')); ?>
                <? else : ?>
                    <?= Icon::create('accessibility')->asImg(24) ?>
                    <a href="index.php?set_contrast=1" id="highcontrastlink"><?= _('Hohen Kontrast aktivieren')?></a>
                    <?= tooltipIcon(_('Aktiviert einen hohen Kontrast gemäß WCAG 2.1. Diese Einstellung wird nach dem Login übernommen.
                    Sie können sie in Ihren persönlichen Einstellungen ändern.')); ?>
                <? endif ?>
            </div>

            <div class="login_info">
                <div>
                    <?= _('Aktive Veranstaltungen') ?>:
                    <?= number_format($num_active_courses, 0, ',', '.') ?>
                </div>

                <div>
                    <?= _('Registrierte NutzerInnen') ?>:
                    <?= number_format($num_registered_users, 0, ',', '.') ?>
                </div>

                <div>
                    <?= _('Davon online') ?>:
                    <?= number_format($num_online_users, 0, ',', '.') ?>
                </div>

                <div>
                    <a href="dispatch.php/siteinfo/show?cancel_login=1">
                        <?= _('mehr') ?> &hellip;
                    </a>
                </div>
            </div>
        </footer>

    </div>
</main>

<script type="text/javascript" language="javascript">

    var loginname = document.getElementById("loginname");
    var password = document.getElementById("password");

    var loginname_caps = document.getElementById("loginname_caps");
    var password_caps = document.getElementById("password_caps");

    // When the user presses any key on the keyboard, run the function
    loginname.addEventListener("keyup", function(event) {

        // If "caps lock" is pressed, display the warning text
        if (event.getModifierState("CapsLock")) {
            loginname_caps.style.display = "block";
        } else {
            loginname_caps.style.display = "none"
        }
    });

    // When the user presses any key on the keyboard, run the function
    password.addEventListener("keyup", function(event) {

        // If "caps lock" is pressed, display the warning text
        if (event.getModifierState("CapsLock")) {
            password_caps.style.display = "block";
        } else {
            password_caps.style.display = "none"
        }
    });


    var togglePassword = document.getElementById('password_toggle')

    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });

</script>
