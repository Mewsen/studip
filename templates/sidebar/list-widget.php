<ul class="<?= implode(' ', $css_classes) ?>">
<? foreach ($elements as $index => $element): ?>
    <? $icon = $element->icon ?? null; ?>
    <? if ($icon && $element instanceof LinkElement && $element->isDisabled()): ?>
        <? $icon = $icon->copyWithRole('inactive') ?>
    <? endif ?>
    <li id="<?= htmlReady($index) ?>"
        <?= $icon ? 'style="' . $icon->asCSS() .'"' : '' ?>
        <?= ($element->active ?? false) ? 'class="active"' : '' ?>>
        <?= $element->render() ?>
    </li>
<? endforeach; ?>
</ul>
