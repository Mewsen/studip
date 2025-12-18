<?php

namespace DashboardWidget\WidgetTypes\Contact;

/**
 * The Contact Widget Type Single Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class ContactSingleWidget extends ContactWidgetType
{
    /**
     * @inheritdoc
     */
    public static function getScope(): string
    {
        return 'single';
    }

    /**
     * @inheritdoc
     */
    public static function getTitle(): string
    {
        return _('Kontakte Single'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('Es bietet einfachen Zugriff zum Single Kontakt.'); // TODO: Desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('Kontakte Single'), // TODO: Title?
            'contact_id' => null
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
