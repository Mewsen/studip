<?php
/**
 * @var array $attributes
 * @var string $baseComponent
 * @var array $props
 * @var array $slots
 * @var array $storeData
 */
?>
<? foreach ($storeData as $store => $data): ?>
<script type="application/json" id="vue-store-data-<?= htmlReady($store) ?>"><?= json_encode($data) ?></script>
<? endforeach; ?>
<div <?= arrayToHtmlAttributes($attributes) ?>>
    <<?= strtokebabcase($baseComponent) ?> <?= arrayToHtmlAttributes($props) ?>>
    <? foreach ($slots as $name => $slot): ?>
        <template #<?= htmlReady($name) ?>>
            <?= $slot ?>
        </template>
    <? endforeach; ?>
    </<?= strtokebabcase($baseComponent) ?>>
</div>
