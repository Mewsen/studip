<?php

namespace DashboardWidget\WidgetTypes\Group;

/**
 * The Interest Group Widget Type Single Variant
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class GroupSingleWidget extends GroupWidgetType
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
        return _('InterestGroup Single'); // TODO: Title?
    }

    /**
     * @inheritdoc
     */
    public static function getDescription(): string
    {
        return _('Es bietet einfachen Zugriff zum InterestGroup Single.'); // TODO: Desc?
    }

    /**
     * @inheritdoc
     */
    public function initialPayload(): array
    {
        return [
            'title' => _('InterestGroup Single'), // TODO: Title?
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
}
