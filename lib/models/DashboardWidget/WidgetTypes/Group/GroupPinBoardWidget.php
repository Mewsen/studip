<?php

namespace DashboardWidget\WidgetTypes\Group;

/**
 * The Interest Group Widget Type Pin Board Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class GroupPinBoardWidget extends GroupWidgetType
{
    /**
     * @inheritdoc
     */
    public static function getScope(): string
    {
        return 'pinboard';
    }

    /**
     * @inheritdoc
     */
    public static function getTitle(): string
    {
        return _('InterestGroup Pin Board'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('Es bietet einfachen Zugriff zum InterestGroup Pin Board.'); // TODO: desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('InterestGroup Pin Board'), // TODO: Title?
            'group_id' => null
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
