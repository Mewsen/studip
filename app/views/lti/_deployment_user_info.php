<?
/**
 * @var LtiDeployment $deployment
 */
?>
<? if (!empty($deployment)) : ?>
    <article class="studip">
        <header><h1><?= htmlReady($deployment->title) ?></h1></header>
        <section>
            <? if ($deployment->tool->is_global) : ?>
                <p>
                    <?= sprintf(
                        'Dies ist eine Einbindung des LTI-Tools „%s“.',
                        htmlReady($deployment->tool->name)
                    ) ?>
                </p>
            <? endif ?>
            <p><?= formatReady($deployment->description ?? '') ?></p>
            <?
            $url_parts = parse_url($deployment->getLaunchURL());
            ?>
            <? if (!empty($url_parts['host'])) : ?>
                <p><?= _('Domain') ?>: <?= htmlReady($url_parts['host']) ?></p>
            <? endif ?>
            <? if ($deployment->tool->terms_of_use_url || $deployment->tool->privacy_policy_url) : ?>
                <p>
                    <? if ($deployment->tool->terms_of_use_url) : ?>
                        <a href="<?= htmlReady($deployment->tool->terms_of_use_url) ?>">
                            <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                            <?= _('Nutzungsbedingungen') ?>
                        </a>
                    <? endif ?>
                    <? if ($deployment->tool->privacy_policy_url) : ?>
                        <a href="<?= htmlReady($deployment->tool->privacy_policy_url) ?>">
                            <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                            <?= _('Datenschutzerklärung') ?>
                        </a>
                    <? endif ?>
                </p>
            <? endif ?>
        </section>
    </article>
<? endif ?>
