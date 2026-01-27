<?php
/**
 * @var array $messages
 */
?>

<ul class="messages-container">
    <? foreach ($messages as $message): ?>
        <li>
            <? if ($message['type'] == 'error'): ?>
                <?= MessageBox::error($message['text']) ?>
            <? else: ?>
                <?= MessageBox::info($message['text']) ?>
            <? endif; ?>
        </li>
    <? endforeach ?>
</ul>
