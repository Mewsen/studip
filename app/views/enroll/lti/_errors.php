<?php
/**
 * @var array $errors
 */
?>

<ul class="messages-container">
    <? foreach ($errors as $error): ?>
        <li>
            <?= MessageBox::error($error) ?>
        </li>
    <? endforeach ?>
</ul>
