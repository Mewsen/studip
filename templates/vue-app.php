<?php
/**
 * @var array $attributes
 * @var string $baseComponent
 * @var array $props
 * @var array $storeData
 * @var array $vuexStoreData
 * @var array $slots
 */
?>
<? foreach ($storeData as $store => $data): ?>
<script type="application/json" id="vue-store-data-<?= htmlReady($store) ?>"><?= json_encode($data) ?></script>
<? endforeach; ?>
<? foreach ($vuexStoreData as $store => $data): ?>
<script type="application/json" id="vue-vuex-store-data-<?= htmlReady($store) ?>"><?= json_encode($data) ?></script>
<? endforeach; ?>
<div <?= arrayToHtmlAttributes($attributes) ?>>
    <<?= strtokebabcase($baseComponent) ?> <?= arrayToHtmlAttributes($props) ?>>
    <? foreach ($slots as $slotname => $slot): ?>
        <template #<?= htmlReady($slotname) ?>><?= $slot ?></template>
    <? endforeach; ?>
    </<?= strtokebabcase($baseComponent) ?>>
</div>
