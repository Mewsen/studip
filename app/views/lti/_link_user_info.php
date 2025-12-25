<?
/**
 * @var Lti\ResourceLink $link
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
            <ul>
                <? if (!empty($url_parts['host'])) : ?>
                    <li><?= _('Domain') ?>: <?= htmlReady($url_parts['host']) ?></li>
                <? endif ?>
                <? if ($link->deployment->registration->config_values['terms_of_use_url'] || $link->deployment->registration->config_values['privacy_policy_url']) : ?>
                    <li>
                        <? if ($link->deployment->registration->config_values['terms_of_use_url']) : ?>
                            <a href="<?= htmlReady($link->deployment->registration->config_values['terms_of_use_url']) ?>">
                                <?= Icon::create('link-extern')->asSvg(['class' => 'text-bottom']) ?>
                                <?= _('Nutzungsbedingungen') ?>
                            </a>
                        <? endif ?>
                    </li>
                    <li>
                        <? if ($link->deployment->registration->config_values['privacy_policy_url']) : ?>
                            <a href="<?= htmlReady($link->deployment->registration->config_values['privacy_policy_url']) ?>">
                                <?= Icon::create('link-extern')->asSvg(['class' => 'text-bottom']) ?>
                                <?= _('Datenschutzerklärung') ?>
                            </a>
                        <? endif ?>
                    </li>
                <? endif ?>
            </ul>
        </section>
    </article>
<? endif ?>
