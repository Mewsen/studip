<?php
/**
 * @var Exercise $exercise
 */
?>
<? if (isset($exercise->options['hint']) && $exercise->options['hint'] !== ''): ?>
    <div class="exercise_hint inline-content">
        <h4><?= _('Hinweis:') ?></h4>
        <?= formatReady($exercise->options['hint']) ?>
    </div>
    <br>
<? endif ?>
