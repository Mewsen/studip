<p style="font-size: 14px; text-align: right;">
    <?= strftime('%x', $timestamp) ?>
</p>
<h1 style="font-size: 20px; text-align: center">
    <?= htmlReady($unit->config['certificate']['title']) ?>
</h1>
<h2 style="font-size: 14px; text-align: center">
    <?= sprintf(_('für %s'), htmlReady($user->getFullName())) ?>
</h2>
<p style="font-size: 14px; text-align: center;">
    <?= $unit->config['certificate']['text'] ?>
</p>
