<form class="default" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <ul class="<?= implode(' ', $css_classes) ?>" aria-label="<?= htmlReady($title) ?>">
    <? foreach ($elements as $index => $element): ?>
        <? $icon = $element->icon ?? null ?>
        <? if ($icon && $element instanceof LinkElement && $element->isDisabled()): ?>
            <? $icon = $icon->copyWithRole(Icon::ROLE_INACTIVE) ?>
        <? endif ?>
        <li id="<?= htmlReady($index) ?>"
            <?= !empty($element->active) ? 'class="active"' : '' ?>>
            <? if (isset($icon) && $element instanceof LinkElement): ?>
                <?= $element->renderWithIcon($icon) ?>
            <? else: ?>
                <?= $element->render() ?>
            <? endif ?>
        </li>
    <? endforeach; ?>
    </ul>
</form>
