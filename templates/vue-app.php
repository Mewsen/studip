<?php
/**
 * @var \Studip\VueApp $app
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
<div data-vue-app>
     <script type="application/json"><?= json_encode($data) ?></script>
</div>
