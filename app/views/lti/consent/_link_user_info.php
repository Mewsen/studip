<?
/**
 * @var Lti\ResourceLink $link
 */

$registrationConfigs = $link->deployment->registration->getConfigValues();
$urlParts = parse_url($link->getLaunchURL());
?>
<article class="studip">
    <header><h1><?= htmlReady($link->title) ?></h1></header>
    <section>
        <? if ($link->deployment->registration->range_id === 'global') : ?>
            <p>
                <?= sprintf(
                    'Dies ist eine Einbindung des LTI-Tools „%s“.',
                    htmlReady($link->deployment->registration->name)
                ) ?>
            </p>
        <? endif ?>
        <p><?= formatReady($link->description) ?></p>
        <ul>
            <? if (!empty($urlParts['host'])) : ?>
                <li><?= _('Domain') ?>: <?= htmlReady($urlParts['host']) ?></li>
            <? endif ?>
            <? if (isset($registrationConfigs['terms_of_use_url'])) : ?>
                <li>
                    <a href="<?= htmlReady($registrationConfigs['terms_of_use_url']) ?>">
                        <?= Icon::create('link-extern')->asSvg(['class' => 'text-bottom']) ?>
                        <?= _('Nutzungsbedingungen') ?>
                    </a>
                </li>
            <? endif ?>
            <? if (isset($registrationConfigs['privacy_policy_url'])) : ?>
                <li>
                    <a href="<?= htmlReady($registrationConfigs['privacy_policy_url']) ?>">
                        <?= Icon::create('link-extern')->asSvg(['class' => 'text-bottom']) ?>
                        <?= _('Datenschutzerklärung') ?>
                    </a>
                </li>
            <? endif ?>
        </ul>
    </section>
</article>
