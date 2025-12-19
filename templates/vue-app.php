<?php
/**
 * @var \Studip\VueApp $app
 * @var bool $configOnly
 */
$data = [
    'appPath' => $app->getAppPath(),
    'plugins' => $app->getPlugins(),
    'props' => $app->getProps(),
    'slots' => $app->getSlots(),
    'stores' => $app->getStores(),
    'storeData' => $app->getStoreData(),
    'vuexStores' => $app->getVuexStores(),
    'vuexStoreData' => $app->getVuexStoreData(),
];
?>
<? if (!$configOnly) : ?>
<div data-vue-app>
    <script type="application/json">
<? endif ?>
        <?= json_encode($data) ?>
<? if (!$configOnly) : ?>
    </script>
</div>
<? endif;
