<?php

namespace DashboardWidget\WidgetTypes\Chat;

use DashboardWidget\WidgetTypes\WidgetType;
/**
 * The Chat Widget Type Parent class
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
abstract class ChatWidgetType extends WidgetType
{
    /**
     * @inheritdoc
     */
    public static function getType(): string
    {
        return 'chat';
    }
}
