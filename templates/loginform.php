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
        <header>
            <h1><?= htmlReady(Config::get()->UNI_NAME_CLEAN) ?></h1>
            <h2 style="margin: 0; padding-bottom:10px;"><?=_('Herzlich willkommen!')?></h2>
        </header>

        <form class="default" name="login_form" id="login_form" method="post" action="<?= URLHelper::getLink(Request::url(), ['cancel_login' => NULL]) ?>" style="display:none">

            <section>
                <label>
                    <span class="required"><?= _('Benutzername:') ?></span>
                    <input type="text" <?= mb_strlen($uname) ? '' : 'autofocus' ?>
                           id="loginname" name="loginname"
                           value="<?= htmlReady($uname) ?>"
                           size="20"
                           autocorrect="off" autocapitalize="off" required>
                    <? if (Config::get()->USERNAME_TOOLTIP_ACTIVATED) : ?>
                        <?= tooltipIcon(htmlReady((string)Config::get()->USERNAME_TOOLTIP_TEXT)) ?>
                    <? endif ?>
                </label>
            </section>
            <p id="loginname_caps" style="display: none"><?= _('Feststelltaste ist aktiviert!') ?></p>
            <section>
                <label for="password" style="position: relative">
                    <span class="required"><?= _('Passwort:') ?></span>
                    <input type="password" <?= mb_strlen($uname) ? 'autofocus' : '' ?>
                           id="password" name="password" size="20" required>

                    <i id="password_toggle" href=""
                        <?= tooltip(_('Passwort zeigen/verstecken'), true) ?>>
                        <?= Icon::create('visibility-checked')->asImg(20, ['id' => 'visible-password']) ?>
                        <?= Icon::create('visibility-invisible')->asImg(20, ['id' => 'invisible-password']) ?>

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
            <?= Button::createAccept(_('Anmelden'), _('Login'), ['id' => 'submit_login']); ?>

            <div>
                <? if (Config::get()->ENABLE_REQUEST_NEW_PASSWORD_BY_USER && in_array('Standard', $GLOBALS['STUDIP_AUTH_PLUGIN'])): ?>
                <a href="<?= URLHelper::getLink('dispatch.php/new_password?cancel_login=1') ?>">
                    <? else: ?>
                    <a href="mailto:<?= $GLOBALS['UNI_CONTACT'] ?>?subject=<?= rawurlencode('Stud.IP Passwort vergessen - '.Config::get()->UNI_NAME_CLEAN) ?>&amp;body=<?= rawurlencode('Ich habe mein Passwort vergessen. Bitte senden Sie mir ein Neues.\nMein Nutzername: ' . htmlReady($uname) . "\n") ?>">
                        <? endif; ?>
                        <?= _('Passwort vergessen?') ?>
                    </a>
            </div>
        </form>


        <nav>
            <ul>
                <li class="login_link">
                    <a href="#" id="toggle_login">Standard-Login</a>
                </li>
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
                    <a href="index.php?set_language=<?= $temp_language_key ?>&cancel_login=1">
                        <?= htmlReady($temp_language['name']) ?>
                    </a>
                <? endforeach; ?>
            </div>

            <div id="contrast">
                <? if (isset($_SESSION['contrast'])) : ?>
                    <?= Icon::create('accessibility')->asImg(24) ?>
                    <a href="index.php?unset_contrast=1&cancel_login=1"><?= _('Normalen Kontrast aktivieren') ?></a>
                    <?= tooltipIcon(_('Aktiviert standardmäßige, nicht barrierefreie Kontraste.')); ?>
                <? else : ?>
                    <?= Icon::create('accessibility')->asImg(24) ?>
                    <a href="index.php?set_contrast=1&cancel_login=1" id="highcontrastlink"><?= _('Hohen Kontrast aktivieren')?></a>
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

    <? if (count($faq_entries) > 0) : ?>
        <div id="newsbox">
            <h1><?= _('FAQ zum Login') ?></h1>
        <? foreach ($faq_entries as $entry) : ?>
            <article class="studip toggle">
                <header>
                    <h1><a><?= htmlReady($entry->title) ?></a></h1>
                </header>
                <section><?= formatReady($entry->description) ?>
                </section>
            </article>
        <? endforeach ?>
        </div>

    <? endif ?>
</main>

