<?php
/**
 * @var array $messages
 */
?>

<ul>
    <? foreach ($messages as $message): ?>
    <li>
        <?= $message['text'] ?>
    </li>
    <? endforeach ?>
</ul>
