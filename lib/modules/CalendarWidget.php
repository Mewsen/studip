<?php
/**
 * CalendarWidget.php
 *
 * @author  Murtaza Sultani <sultani@data-quest.de>
 * @license GPL2 or any later version
 * @since   Stud.IP 6.1
 */

class CalendarWidget extends CorePlugin implements PortalPlugin
{
    /**
     * Returns the name of the plugin/widget.
     *
     * @return String containing the name
     */
    public function getPluginName()
    {
        return _('Kalender');
    }

    public function getMetadata()
    {
        return [
            'description' => _('Mit diesem Widget haben Sie eine Übersicht über Ihren Kalender.')
        ];
    }

    /**
     * Return the template for the widget.
     *
     * @return Flexi\PhpTemplate The template containing the widget contents
     */
    public function getPortalTemplate()
    {
        $template = $GLOBALS['template_factory']->open('start/calendar_widget');
        $template->fullcalendar = \Studip\Calendar\Helper::getPersonalFullcalendar()->render();
        return $template;
    }
}
