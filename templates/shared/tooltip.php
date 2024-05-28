<span class="tooltip tooltip-icon <? if ($important) echo 'tooltip-important'; ?>"
      tabindex="0" aria-label="<?= $html ? kill_format($text) : htmlReady($text) ?>">
    <span class="tooltip-content"><?= $html ? $text : htmlReady($text) ?></span>
</span>
