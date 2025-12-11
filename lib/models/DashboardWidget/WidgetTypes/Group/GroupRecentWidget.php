<?php

namespace DashboardWidget\WidgetTypes\Group;

/**
 * The Contact Widget Type recent Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class GroupRecentWidget extends GroupWidgetType
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
        return _('InterestGroup Recent'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('InterestGroup Recent'); // TODO: Desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('InterestGroup Recent'), // TODO: Title?
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
