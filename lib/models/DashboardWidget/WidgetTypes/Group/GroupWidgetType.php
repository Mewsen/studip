<?php

namespace DashboardWidget\WidgetTypes\Group;

use DashboardWidget\WidgetTypes\WidgetType;
/**
 * The Interest Group Widget Type Parent class
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
abstract class GroupWidgetType extends WidgetType
{
    /**
     * @inheritdoc
     */
    public static function getType(): string
    {
        return 'group';
    }
}
