<?php

namespace DashboardWidget;

/**
 * Interface for a DashboardWidget plugin.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
interface DashboardWidgetPlugin
{
    /**
     * Implement this method to register more widget types.
     *
     * You get the current list of widget types and must return an updated list
     * containing your own widget types.
     *
     * the format should be ['{type}.{scope}' => 'the full class name']
     * example:
     *  [
     *     "chat.conversation": "DashboardWidget\WidgetTypes\Chat\ChatConversationWidget",
     *     "chat.recent": "DashboardWidget\WidgetTypes\Chat\ChatRecentWidget",
     *     "chat.selection": "DashboardWidget\WidgetTypes\Chat\ChatSelectionWidget",
     *     ...
     *  ]
     *
     * @param array $otherWidgetTypes the current list of widget types
     *
     * @return array the updated list of widget types
     */
    public function registerWidgetTypes(array $otherWidgetTypes): array;
}
