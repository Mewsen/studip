<?
/**
 * @var LtiTool $tool
 * @var LtiDeployment $deployment
 */
?>
<? if ($tool) : ?>
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
                    <a href="<?= htmlReady($deployment->launch_url) ?>"><?= htmlReady($deployment->launch_url) ?></a>
                <? else : ?>
                    <a href="<?= htmlReady($tool->launch_url) ?>"><?= htmlReady($tool->launch_url) ?></a>
                <? endif ?>
            </dd>

            <? if ($deployment) : ?>
                <dt><?= _('Deployment-ID') ?></dt>
                <dd><?= htmlReady($deployment->id) ?></dd>

                <? if ($parameters = $deployment->getCustomParameters()) : ?>
                    <dt><?= _('LTI custom parameters') ?></dt>
                    <dd><?= htmlReady($parameters) ?></dd>
                <? endif ?>
            <? endif ?>
        </dl>
    </article>
    <article class="studip">
        <header><h1><?= _('Plattform-Konfiguration') ?></h1></header>
        <?= $this->render_partial('lti/_platform_data', ['platform' => \Studip\LTI13a\PlatformManager::getPlatformConfiguration()]) ?>
    </article>
<? endif ?>
