<?php

namespace DashboardWidget;

use DashboardWidget\WidgetTypes\WidgetType;
use JSONArrayObject;
use User;

/**
 * DashboardWidget's container model.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 *
 * @property int $id database column
 * @property string $owner_id database column
 * @property string $context database column
 * @property string $context_id database column
 * @property \JSONArrayObject $payload database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \User $owner belongs_to \User
 * @property \SimpleORMapCollection<Widget> $widgets has_many Widgets
 */
class Container extends \SimpleORMap
{
    /** @var string The community context  */
    public const CONTEXT_COMMUNITY = 'community';

    /** @var array All available contexts  */
    public const ALL_CONTEXTS = [
        self::CONTEXT_COMMUNITY,
    ];

    /** @var string XXL Breakpoint identifier  */
    public const BREAKPOINT_XXL = 'xxl';

    /** @var string XL Breakpoint identifier  */
    public const BREAKPOINT_XL = 'xl';

    /** @var string LG Breakpoint identifier  */
    public const BREAKPOINT_LG = 'lg';

    /** @var string MD Breakpoint identifier  */
    public const BREAKPOINT_MD = 'md';

    /** @var string SM Breakpoint identifier  */
    public const BREAKPOINT_SM = 'sm';

    /** @var string XS Breakpoint identifier  */
    public const BREAKPOINT_XS = 'xs';

    /** @var string XXS Breakpoint identifier  */
    public const BREAKPOINT_XXS = 'xxs';

    /** @var array All defined breakpoints  */
    public const ALL_BREAKPOINTS = [
        self::BREAKPOINT_XXL,
        self::BREAKPOINT_XL,
        self::BREAKPOINT_LG,
        self::BREAKPOINT_MD,
        self::BREAKPOINT_SM,
        self::BREAKPOINT_XS,
        self::BREAKPOINT_XXS,
    ];

    /** @var array all default (commonly-used) breakpoints  */
    public const DEFAULT_BREAKPOINTS = [
        self::BREAKPOINT_XL,
        self::BREAKPOINT_LG,
        self::BREAKPOINT_MD,
        self::BREAKPOINT_SM,
    ];

    /** @var array Available breakpoints screen width mapping  */
    public const BREAKPOINT_WIDTHS = [
        self::BREAKPOINT_XXL => 2530,
        self::BREAKPOINT_XL => 1890,
        self::BREAKPOINT_LG => 1410,
        self::BREAKPOINT_MD => 990,
        self::BREAKPOINT_SM => 768,
        self::BREAKPOINT_XS => 400,
        self::BREAKPOINT_XXS => 0,
    ];

    /** @var array Available breakpoints size naming mapping  */
    public const BREAKPOINT_SIZE_MAPPINGS = [
        self::BREAKPOINT_XXL => 'desktop-large',
        self::BREAKPOINT_XL => 'desktop',
        self::BREAKPOINT_LG => 'desktop-small',
        self::BREAKPOINT_MD => 'tablet',
        self::BREAKPOINT_SM => 'smartphone',
        self::BREAKPOINT_XS => 'smartphone-small',
        self::BREAKPOINT_XXS => 'smartphone-tiny',
    ];

    /** @var array Available breakpoints columns mapping  */
    public const BREAKPOINT_COLS = [
        self::BREAKPOINT_XXL => 20,
        self::BREAKPOINT_XL => 16,
        self::BREAKPOINT_LG => 12,
        self::BREAKPOINT_MD => 10,
        self::BREAKPOINT_SM => 8,
        self::BREAKPOINT_XS => 4,
        self::BREAKPOINT_XXS => 2,
    ];

    /** @var int default width to columns ratio  */
    public const DEFAULT_COL_WIDTH_RATIO = 2;

    /** @var int default height to columns ratio  */
    public const DEFAULT_COL_HEIGHT_RATIO = 2;

    /** @var string default context id, to b used when there is no context id needed.  */
    public const DEFAULT_CONTEXT_ID = 'generic';

    /**
     * @inheritdoc
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'dashboard_widget_containers';

        $config['serialized_fields']['payload'] = JSONArrayObject::class;

        $config['has_many']['widgets'] = [
            'class_name' => Widget::class,
            'assoc_foreign_key' => 'container_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
        ];

        $config['belongs_to']['owner'] = [
            'class_name' => User::class,
            'foreign_key' => 'owner_id',
        ];

        parent::configure($config);
    }

    /**
     * Adds/Updates a widget data including widget id and its position for a specific breakpoint into the container's payload.
     *
     * When a new widget is going to be added it would also be added to every other defined breakpoints in the payload with the calculation of its next best position in each breakpoint.
     *
     * NOTE: This method does NOT store the changes!
     *
     * @param Widget $widget the targeted widget's record.
     * @param string $breakpoint the flag determining the targeted breakpoint.
     * @param array $position the position set of the widget for the breakpoint in the container.
     *              ['x' => int, 'y' => int, 'w' => int, 'h' => int]
     * @param bool $isNew a flag to determine of the record is new or not, determining the add or update capability.
     * @return void
     */
    public function addUpdateWidgetInPayload(Widget $widget, string $breakpoint, array $position, bool $isNew = true): void
    {
        $payload = $this->payload->getArrayCopy();
        $payload = $this->removeExistingPayloadObjIn($payload, $breakpoint, $widget->id);
        $payloadArr = $position;
        $payloadArr['i'] = $widget->id;
        $payload[$breakpoint][] = $payloadArr;
        // When we add a new widget, we also need to add it other breakpoints by calculating the next best position.
        if ($isNew) {
            foreach ($payload as $bp => $content) {
                if ($bp === $breakpoint) {
                    continue;
                }
                $lastPos = $this->calculateLastPosition($bp, $content);
                $payload[$bp][] = $lastPos + ['i' => $widget->id];
            }
        }
        $this->payload->exchangeArray($payload);
    }

    /**
     * Removes a widget from all available breakpoints in the container's payload using the widget's id.
     *
     * NOTE: This method does NOT store the changes!
     *
     * @param int $widgetId
     * @return void
     */
    public function removeWidgetFromPayload(int $widgetId): void
    {
        $payload = $this->payload->getArrayCopy();
        $newPayload = $payload;
        // If a widget is getting removed from a breakpoint, we ought to remove it also from others!
        foreach ($payload as $breakpoint => $value) {
            $newPayload = $this->removeExistingPayloadObjIn($newPayload, $breakpoint, $widgetId);
        }

        $this->payload->exchangeArray($newPayload);
    }

    /**
     * Calculates the next best position that a widget can have withing the targeted breakpoint, using the defined breakpoint's columns and teh default width & height columns ratio.
     *
     * @param string $breakpoint the targeted breakpoint
     * @param array $currentContent the current position contents of the breakpoint containing other widgets position data.
     * @return array the array containing the next best position of a widget in the breakpoint.
     */
    private function calculateLastPosition(string $breakpoint, array $currentContent): array
    {
        $cols = self::BREAKPOINT_COLS[$breakpoint] ?? 12;
        $defaultW = self::DEFAULT_COL_WIDTH_RATIO;
        $defaultH = self::DEFAULT_COL_HEIGHT_RATIO;

        $nextY = 0;
        while (true) {
            for ($nextX = 0; $nextX < $cols; $nextX++) {
                $nextPos = [
                    'x' => $nextX,
                    'y' => $nextY,
                    'h' => $defaultW,
                    'w' => $defaultH
                ];
                $alreadyOccupied = false;
                foreach ($currentContent as $item) {
                    if (
                        $nextX < ($item['x'] + $item['w']) &&
                        $nextX + ($defaultW > $item['x']) &&
                        $nextY < ($item['y'] + $item['h']) &&
                        ($nextY + $defaultH) > $item['y']
                    ) {
                        $alreadyOccupied = true;
                        break;
                    }
                }

                if (!$alreadyOccupied) {
                    return $nextPos;
                }
            }
            $nextY++;
        }
    }

    /**
     * Removes a position set of a widget from the breakpoint content within the payload.
     *
     * @param array $payload the whole payload to remove from.
     * @param string $breakpoint the targeted breakpoint to remove position object from.
     * @param int $widgetId the widget id
     * @return array
     */
    private function removeExistingPayloadObjIn(array $payload, string $breakpoint, int $widgetId)
    {
        // We go old school here, just to be safe!
        $filteredSet = [];
        foreach ($payload[$breakpoint] as $data) {
            if ((int) $data['i'] === (int) $widgetId) {
                continue;
            }
            $filteredSet[] = $data;
        }
        $payload[$breakpoint] = $filteredSet;
        return $payload;
    }

    /**
     * Finds the container record based on user id, the context and optionally the context id.
     *
     * @param string $userId
     * @param string $context
     * @param string $contextId
     * @return null|Container
     */
    public static function findByUserContext(string $userId, string $context, string $contextId): ?self
    {
        return self::findOneBySQL('owner_id = ? AND context = ? AND context_id = ?',
        [
            $userId,
            $context,
            $contextId
        ]);
    }

    /**
     * Tries to find (and create a new one if not exists) the container record based on user id, the context and optionally the context id.
     * @param string $userId
     * @param string $context
     * @param string $contextId
     * @return Container
     */
    public static function ensureUserContextContainerExists(string $userId, string $context, string $contextId): self
    {
        if (!$entry = self::findByUserContext($userId, $context, $contextId)) {
            $data = [
                'owner_id' => $userId,
                'context' => $context,
                'context_id' => $contextId,
                'payload' => self::getEmptyPayload(),
            ];

            $entry = self::create($data);
        }

        return $entry;
    }

    /**
     * Returns the miscellaneous info of DashboardWidget package.
     *
     * @return array the data to be read from json api from frontend
     */
    public static function getMiscellaneous(): array
    {
        return [
            'widget-types' => WidgetType::getWidgetTypes(),
            'contexts' => self::ALL_CONTEXTS,
            'breakpoints' => self::ALL_BREAKPOINTS,
            'breakpoints-widths' => self::BREAKPOINT_WIDTHS,
            'breakpoints-cols' => self::BREAKPOINT_COLS,
            'breakpoints-mapping' => self::BREAKPOINT_SIZE_MAPPINGS,
        ];
    }

    /**
     * Returns an associative array containing breakpoints as its keys with empty values,
     * considered as an empty payload of the container.
     * @return array
     */
    public static function getEmptyPayload(): array
    {
        return array_fill_keys(self::ALL_BREAKPOINTS, []);
    }
}
