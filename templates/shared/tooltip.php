<?php
/**
 * @var bool $important
 * @var bool $html
 * @var string $text
 */
?>
<span class="as-link tooltip tooltip-icon <? if ($important) echo 'tooltip-important'; ?>"
      tabindex="0"
      data-tooltip
      aria-label="<?= htmlReady($html ? strip_tags($text) : $text) ?>"
>
    <span class="tooltip-content"><?= $html ? $text : htmlReady($text) ?></span>
</span>
