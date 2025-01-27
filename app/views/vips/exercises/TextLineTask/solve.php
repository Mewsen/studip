<?php
/**
 * @var array $response
 */
?>
<label>
    <?= _('Antwort') ?>
    <input type="text" class="character_input" name="answer[0]" value="<?= htmlReady($response[0] ?? '') ?>">
</label>
