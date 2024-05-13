<?
/**
 * @var LtiDeployment $deployment
 */
?>
<? if (!empty($deployment)) : ?>
    <article class="studip">
        <header><h1><?= htmlReady($deployment->title) ?></h1></header>
        <section>
            <dl>
                <? if ($deployment->tool->is_global) : ?>
                    <dt><?= _('Zugehöriges LTI-Tool') ?></dt>
                    <dd><?= htmlReady($deployment->tool->name) ?></dd>
                <? endif ?>
                <dt><?= _('URL') ?></dt>
                <dd><?= htmlReady($deployment->getLaunchURL()) ?> (TODO: Audience-URL)</dd>
            </dl>
        </section>
    </article>
    <? if ($deployment->description || $deployment->tool->terms_of_use_url || $deployment->tool->privacy_policy_url) : ?>
        <article class="studip">
            <header><h1><?= _('Beschreibung') ?></h1></header>
            <section><?= formatReady($deployment->description ?? '') ?></section>
            <? if ($deployment->tool->terms_of_use_url || $deployment->tool->privacy_policy_url) : ?>
                <section>
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
                </section>
            <? endif ?>
        </article>
    <? endif ?>
    <? if ($deployment->data_protection_notes) : ?>
        <article class="studip">
            <header><h1><?= _('Zusätzliche Datenschutzhinweise') ?></h1></header>
            <section><?= formatReady($deployment->data_protection_notes) ?></section>
        </article>
    <? endif ?>
<? endif ?>
