<?php
/**
 * @var bool $important
 * @var bool $html
 * @var string $text
 * @var bool $alt_info
 */
?>
<span
    class="as-link tooltip tooltip-icon
        <? if ($important) echo 'tooltip-important'; ?>
        <? if ($alt_info)  echo 'tooltip-info-alt'; ?>"
    tabindex="0"
    role="tooltip"
    data-tooltip
    aria-label="<?= htmlReady($html ? strip_tags($text) : $text) ?>"
>
    <span class="tooltip-content"><?= $html ? $text : htmlReady($text) ?></span>
</span>
