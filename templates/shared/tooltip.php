<?php
/**
 * @var bool $important
 * @var bool $html
 * @var string $text
 */
?>
<span class="tooltip tooltip-icon <? if ($important) echo 'tooltip-important'; ?>"
      tabindex="0" aria-label="<?= $html ? htmlReady(strip_tags($text)) : htmlReady($text) ?>">
    <span class="tooltip-content"><?= $html ? $text : htmlReady($text) ?></span>
</span>
