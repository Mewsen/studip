<?php
/**
 * @var Resources_LocationController $controller
 * @var bool $show_form
 * @var string $mode
 * @var string $name
 * @var string $category_id
 * @var string $longitude
 * @var string $latitude
 * @var string $altitude
 * @var string $description
 * @var string $parent_id
 * @var string $number
 * @var string $address
 * @var int $sort_position
 * @var Building $location
 * @var array $grouped_defined_properties
 * @var array $property_data
 */
?>
<? if ($show_form): ?>
    <form class="default" method="post" action="<?= $mode === 'add'
        ? $controller->add(['category_id' => $category_id])
        : $controller->edit($location->id) ?>"
          data-dialog="reload-on-close">

        <?= CSRFProtection::tokenTag() ?>
        <fieldset>
            <legend><?= _('Grunddaten') ?></legend>
            <label>
                <?= _('Name des Standortes') ?>
                <input type="text" name="name" value="<?= htmlReady($name ?? '') ?>">
            </label>
            <label>
                <?= _('Beschreibungstext') ?>
                <input type="text" name="description" value="<?= htmlReady($description ?? '') ?>">
            </label>
            <? if ($GLOBALS['perm']->have_perm('root')): ?>
                <label>
                    <?= _('Sortierposition') ?>
                    <input type="text" name="sort_position"
                           value="<?= htmlReady($sort_position ?? '') ?>">
                </label>
            <? endif ?>
            <?= $this->render_partial(
                '../../templates/resources/position_attribute_form_part.php',
                [
                    'property_name' => 'geo_coordinates',
                    'latitude'      => $latitude ?? '',
                    'longitude'     => $longitude ?? '',
                    'altitude'      => $altitude ?? ''
                ]
            ) ?>
        </fieldset>
        <? if (!empty($defined_properties)) : ?>
            <?= $this->render_partial(
                'resources/resource/_standard_properties_form_part.php',
                [
                    'defined_properties' => $defined_properties,
                    'property_data'      => $property_data ?? []
                ]
            ) ?>
        <? endif ?>
        <div data-dialog-button>
            <?= \Studip\Button::create(_('Speichern'), 'save') ?>
        </div>
    </form>
<? endif ?>
