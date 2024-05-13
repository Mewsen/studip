<?
/**
 * @var LtiTool $tool
 * @var LtiDeployment $deployment
 * @var StudipControlle $controller
 */
?>
<? if (!empty($tool)) : ?>
    <article class="studip">
        <header>
            <? if ($deployment) : ?>
                <h1><?= htmlReady($deployment->title) ?></h1>
            <? else : ?>
                <h1><?= htmlReady($tool->name) ?></h1>
            <? endif ?>
        </header>
        <dl>
            <dt><?= _('Launch-URL') ?></dt>
            <dd>
                <? if ($deployment && $deployment->launch_url) : ?>
                    <a href="<?= htmlReady($deployment->launch_url) ?>">
                        <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                        <?= htmlReady($deployment->launch_url) ?>
                    </a>
                <? else : ?>
                    <a href="<?= htmlReady($tool->launch_url) ?>">
                        <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                        <?= htmlReady($tool->launch_url) ?>
                    </a>
                <? endif ?>
            </dd>

            <? if ($tool->terms_of_use_url) : ?>
                <dt><?= _('Nutzungsbedingungen') ?></dt>
                <dd>
                    <a href="<?= htmlReady($tool->terms_of_use_url) ?>">
                        <?= Icon::create('link-extern') ?>
                        <?= htmlReady($tool->terms_of_use_url) ?>
                    </a>
                </dd>
            <? endif ?>
            <? if ($tool->privacy_policy_url) : ?>
                <dt><?= _('Datenschutzerklärung') ?></dt>
                <dd>
                    <a href="<?= htmlReady($tool->privacy_policy_url) ?>">
                        <?= Icon::create('link-extern') ?>
                        <?= htmlReady($tool->terms_of_use_url) ?>
                    </a>
                </dd>
            <? endif ?>

            <? if ($deployment) : ?>
                <dt><?= _('Deployment-ID') ?></dt>
                <dd><?= htmlReady($deployment->id) ?></dd>

                <? if ($parameters = $deployment->getCustomParameters()) : ?>
                    <dt><?= _('LTI custom parameters') ?></dt>
                    <dd><?= htmlReady($parameters) ?></dd>
                <? endif ?>
            <? endif ?>
            <dt><?= _('Direktlink zum LTI-Tool') ?></dt>
            <dd>
                <a href="<?= $controller->link_for('course/lti/iframe', $deployment->id) ?>">
                    <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                    <?= $controller->link_for('course/lti/iframe', $deployment->id) ?>
                </a>
            </dd>
        </dl>
    </article>
    <article class="studip">
        <header><h1><?= _('Plattform-Konfiguration') ?></h1></header>
        <?= $this->render_partial('lti/_platform_data', ['platform' => \Studip\LTI13a\PlatformManager::getPlatformConfiguration()]) ?>
    </article>
<? endif ?>
