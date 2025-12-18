<?php

namespace DashboardWidget\WidgetTypes\Chat;

/**
 * THe Chat Widget Type Selection Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ChatSelectionWidget extends ChatWidgetType
{
    /**
     * @inheritdoc
     */
    public static function getScope(): string
    {
        return 'selection';
    }

    /**
     * @inheritdoc
     */
    public static function getTitle(): string
    {
        return _('Chat Selection'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('Es bietet einfachen Zugriff zum Selection Chat.'); // TODO: Desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('Chat Selection'), // TODO: Title?
            'thread_ids' => []
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getJsonSchema(): string
    {
        $scope = self::getScope();
        $schemaFile = __DIR__ . "/{$scope}.json";
        return file_get_contents($schemaFile);
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultSize(): array
    {
        return [
            'w' => \DashboardWidget\Container::DEFAULT_COL_WIDTH_RATIO,
            'h' => \DashboardWidget\Container::DEFAULT_COL_HEIGHT_RATIO,
        ];
    }
}
