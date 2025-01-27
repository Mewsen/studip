<?php
/**
 * @var Studip\Forms\Form $form
 * @var SimpleORMapCollection $conflicts
 * @var array $stgteil_versions
 * @var string $fullcalendar
 */
?>
<?= $form->render() ?>
<br>
<? if (count($conflicts)) : ?>
    <article class="studip">
        <header>
            <h1><? printf(_('%s Konflikte'), count($conflicts)) ?></h1>
        </header>
        <?= $this->render_partial('admin/overlapping/overlapping') ?>
    </article>
<? endif; ?>
