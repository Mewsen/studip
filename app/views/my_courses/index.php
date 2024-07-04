<?php
/**
 * @var Studip\VueApp $vueApp
 * @var array $my_bosses
 * @var array $waiting_list
 */
?>
<? if ($waiting_list) : ?>
    <?= $this->render_partial('my_courses/waiting_list.php', compact('waiting_list')) ?>
<? endif ?>

<?= $vueApp->render() ?>

<? if (count($my_bosses) > 0) : ?>
    <?= $this->render_partial('my_courses/_deputy_bosses'); ?>
<? endif ?>
