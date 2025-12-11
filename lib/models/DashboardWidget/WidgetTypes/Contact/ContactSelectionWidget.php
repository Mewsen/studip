<?php

namespace DashboardWidget\WidgetTypes\Contact;

/**
 * The Contact Widget Type Selection Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ContactSelectionWidget extends ContactWidgetType
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
        return _('Kontakt Selection'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('Es bietet einfachen Zugriff zum Selection Kontakte.'); // TODO: Desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('Kontakt Selection'), // TODO: Title?
            'contact_ids' => []
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
