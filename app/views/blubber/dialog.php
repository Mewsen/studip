<?= $this->render_partial('blubber/index') ?>

<div data-dialog-button>
    <?= \Studip\LinkButton::create(_('Zum Kontext springen'), $thread->getURL()) ?>
</div>

<script>
jQuery(function ($) {
    <? if ($is_compose) : ?>
        STUDIP.Dialog.close();
        setTimeout(() => {
            STUDIP.Dialog.fromURL(
                "<?= $thread->getURL() ?>"
            );
        }, 500);
    <? endif ?>
});
</script>
