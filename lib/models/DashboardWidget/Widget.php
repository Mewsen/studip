<?php

namespace DashboardWidget;

use JSONArrayObject;

/**
 * DashboardWidget's widget model.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 *
 * @property int $id database column
 * @property int $container_id database column
 * @property string $type database column
 * @property \JSONArrayObject $payload database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \DashboardWidget\Container $container belongs_to \DashboardWidget\Container
 * @property \DashboardWidget\WidgetTypes\WidgetType $widget_type additional field
 */
class Widget extends \SimpleORMap
{
    /**
     * @inheritdoc
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'dashboard_widgets';

        $config['serialized_fields']['payload'] = JSONArrayObject::class;

        $config['belongs_to']['container'] = [
            'class_name' => Container::class,
            'foreign_key' => 'container_id',
        ];

        $config['additional_fields']['widget_type'] = [
            'get' => function ($widget) {
                return WidgetTypes\WidgetType::factory($widget);
            },
        ];

        parent::configure($config);
    }
}
