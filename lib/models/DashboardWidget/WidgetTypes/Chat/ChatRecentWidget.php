<?php

namespace DashboardWidget\WidgetTypes\Chat;

/**
 * THe Chat Widget Type Recent Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ChatRecentWidget extends ChatWidgetType
{
    /**
     * @inheritdoc
     */
    public static function getScope(): string
    {
        return 'recent';
    }

    /**
     * @inheritdoc
     */
    public static function getTitle(): string
    {
        return _('Chat Recent'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('Es bietet einfachen Zugriff zum Recent Chat.'); // TODO: Desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('Chat Recent'), // TODO: Title?
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
}
