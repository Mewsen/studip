<?php

namespace DashboardWidget\WidgetTypes\Chat;

/**
 * THe Chat Widget Type Conversation Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ChatConversationWidget extends ChatWidgetType
{
    /**
     * @inheritdoc
     */
    public static function getScope(): string
    {
        return 'conversation';
    }

    /**
     * @inheritdoc
     */
    public static function getTitle(): string
    {
        return _('Chat Conversation'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('Es bietet einfachen Zugriff zum Conversation Chat.'); // TODO: Desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('Chat Conversation'), // TODO: Title?
            'thread_id' => null
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
