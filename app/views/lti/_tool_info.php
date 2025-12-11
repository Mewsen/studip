<?php
/**
 * @var LtiTool $tool
 * @var \LtiResourceLink $link
 * @var StudipController $controller
 */
?>
<? if (!empty($tool)) : ?>
    <article class="studip">
        <header>
            <? if ($link) : ?>
                <h1><?= htmlReady($link->title) ?></h1>
            <? else : ?>
                <h1><?= htmlReady($tool->name) ?></h1>
            <? endif ?>
        </header>
        <dl>
            <dt><?= _('Launch-URL') ?></dt>
            <dd>
                <? if ($link && $link->launch_url) : ?>
                    <a href="<?= htmlReady($link->launch_url) ?>">
                        <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                        <?= htmlReady($link->launch_url) ?>
                    </a>
                <? else : ?>
                    <a href="<?= htmlReady($tool->launch_url) ?>">
                        <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                        <?= htmlReady($tool->launch_url) ?>
                    </a>
                <? endif ?>
            </dd>

            <? if ($tool->config_values['terms_of_use_url']) : ?>
                <dt><?= _('Nutzungsbedingungen') ?></dt>
                <dd>
                    <a href="<?= htmlReady($tool->config_values['terms_of_use_url']) ?>">
                        <?= Icon::create('link-extern') ?>
                        <?= htmlReady($tool->config_values['terms_of_use_url']) ?>
                    </a>
                </dd>
            <? endif ?>
            <? if ($tool->config_values['privacy_policy_url']) : ?>
                <dt><?= _('Datenschutzerklärung') ?></dt>
                <dd>
                    <a href="<?= htmlReady($tool->config_values['privacy_policy_url']) ?>">
                        <?= Icon::create('link-extern') ?>
                        <?= htmlReady($tool->config_values['terms_of_use_url']) ?>
                    </a>
                </dd>
            <? endif ?>

            <? if ($tool) : ?>
                <dt><?= _('Client-ID') ?></dt>
                <dd><?= htmlReady($tool->id) ?></dd>
            <? endif ?>

            <? if (!empty($link->deployment->id)) : ?>
                <dt><?= _('Deployment-ID') ?></dt>
                <dd><?= htmlReady($link->deployment->id) ?></dd>

                <? if ($parameters = $link->getCustomParameters()) : ?>
                    <dt><?= _('LTI custom parameters') ?></dt>
                    <dd><?= htmlReady($parameters) ?></dd>
                <? endif ?>
            <? endif ?>
            <? if ($link) : ?>
                <dt><?= _('Direktlink zum LTI-Tool') ?></dt>
                <dd>
                    <ul>
                        <li>
                            <a href="<?= $controller->link_for('course/lti/iframe', $link->id) ?>">
                                <?= Icon::create('link-extern')->asImg(['class' => 'text-bottom']) ?>
                                <?= $controller->link_for('course/lti/iframe', $link->id) ?>
                            </a>
                        </li>
                    </ul>
                </dd>
            <? endif ?>
        </dl>
    </article>
    <article class="studip">
        <header><h1><?= _('Plattform-Konfiguration') ?></h1></header>
        <?= $this->render_partial('lti/_platform_data', ['platform' => \Studip\LTI13a\PlatformManager::getPlatformConfiguration($tool->id)]) ?>
    </article>
<? endif ?>
