<?
/**
 * @var LtiDeployment $deployment
 */
?>
<? if (!empty($deployment)) : ?>
<article class="studip">
    <header>
        <h1><?= htmlReady($deployment->title) ?></h1>
    </header>
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
<? endif ?>
