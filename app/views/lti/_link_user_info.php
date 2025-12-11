<?
/**
 * @var \LtiResourceLink $link
 */
?>
<? if (!empty($link)) : ?>
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
            <p><?= formatReady($link->description ?? '') ?></p>
            <?
            $url_parts = parse_url($link->getLaunchURL());
            ?>
            <? if (!empty($url_parts['host'])) : ?>
                <p><?= _('Domain') ?>: <?= htmlReady($url_parts['host']) ?></p>
            <? endif ?>
            <? if ($link->deployment->registration->config_values['terms_of_use_url'] || $link->deployment->registration->config_values['privacy_policy_url']) : ?>
                <p>
                    <? if ($link->deployment->registration->config_values['terms_of_use_url']) : ?>
                        <a href="<?= htmlReady($link->deployment->registration->config_values['terms_of_use_url']) ?>">
                            <?= Icon::create('link-extern')->asSvg(['class' => 'text-bottom']) ?>
                            <?= _('Nutzungsbedingungen') ?>
                        </a>
                    <? endif ?>
                    <? if ($link->deployment->registration->config_values['privacy_policy_url']) : ?>
                        <a href="<?= htmlReady($link->deployment->registration->config_values['privacy_policy_url']) ?>">
                            <?= Icon::create('link-extern')->asSvg(['class' => 'text-bottom']) ?>
                            <?= _('Datenschutzerklärung') ?>
                        </a>
                    <? endif ?>
                </p>
            <? endif ?>
        </section>
    </article>
<? endif ?>
