<?php

namespace DashboardWidget\WidgetTypes\Contact;

use DashboardWidget\WidgetTypes\WidgetType;
/**
 * The Contact Widget Type Parent class
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
abstract class ContactWidgetType extends WidgetType
{
    /**
     * @inheritdoc
     */
    public static function getType(): string
    {
        return 'contact';
    }
}
