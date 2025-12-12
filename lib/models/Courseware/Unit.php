<?php

namespace Courseware;

use JSONArrayObject;
use User;

/**
 * Courseware's units.
 *
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 5.3
 *
 * @property int $id database column
 * @property string|null $range_id database column
 * @property string|null $range_type database column
 * @property int $structural_element_id database column
 * @property string $content_type database column
 * @property int|null $position database column
 * @property int $public database column
 * @property string|null $creator_id database column
 * @property string $permission_scope database column
 * @property string $permission_type database column
 * @property string $visible database column
 * @property bool $visible_all database column
 * @property int|null $visible_start_date database column
 * @property int|null $visible_end_date database column
 * @property string $writable database column
 * @property bool $writable_all database column
 * @property int|null $writable_start_date database column
 * @property int|null $writable_end_date database column
 * @property \JSONArrayObject $visible_approval database column
 * @property \JSONArrayObject $writable_approval database column
 * @property \JSONArrayObject $config database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \Course|null $course belongs_to \Course
 * @property \User|null $user belongs_to \User
 * @property \User|null $creator belongs_to \User
 * @property StructuralElement $structural_element has_one StructuralElement
 */

class Unit extends \SimpleORMap implements \PrivacyObject, \FeedbackRange
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'cw_units';

        $config['serialized_fields']['config'] = JSONArrayObject::class;
        $config['serialized_fields']['visible_approval'] = JSONArrayObject::class;
        $config['serialized_fields']['writable_approval'] = JSONArrayObject::class;

        $config['has_one']['structural_element'] = [
            'class_name' => StructuralElement::class,
            'foreign_key' => 'structural_element_id',
            'on_delete' => 'delete',
        ];
        $config['belongs_to']['course'] = [
            'class_name' => \Course::class,
            'foreign_key' => 'range_id',
            'assoc_foreign_key' => 'seminar_id',
        ];
        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'range_id',
            'assoc_foreign_key' => 'user_id',
        ];
        $config['belongs_to']['creator'] = [
            'class_name' => User::class,
            'foreign_key' => 'creator_id',
        ];

        $config['registered_callbacks']['after_delete'][] = 'updatePositionsAfterDelete';
        $config['registered_callbacks']['before_delete'][] = 'cbBeforeDelete';

        parent::configure($config);
    }

    public function cbBeforeDelete()
    {
        \FeedbackElement::deleteBySQL('range_id = ? AND range_type = ?', [$this->id, self::class]);
    }

    public static function findCoursesUnits(\Course $course): array
    {
        return self::findBySQL('range_id = ? AND range_type = ?', [$course->id, 'course']);
    }

    public static function findUsersUnits(User $user): array
    {
        return self::findBySQL('range_id = ? AND range_type = ?', [$user->id, 'user']);
    }

    public function canRead(User $user): bool
    {
        if ($this->canEdit($user) || $this->canEditContent($user)) {
            return true;
        }
        if ($this->permission_scope === 'unit') {
            if ($this->permission_type === 'all' || $this->visible_all) {
                if ($this->isVisible()) {
                    return true;
                }
                return false;
            }
            if ($this->permission_type === 'users') {
                return in_array($user->id, json_decode($this->visible_approval)) && $this->isVisible();
            }
            if ($this->permission_type === 'groups') {
                $groups = json_decode($this->visible_approval);
                foreach ($groups as $groupId) {
                    $group = \Statusgruppen::find($groupId);
                    if ($group && $group->isMember($user->id)) {
                        return $this->isVisible();
                    }
                }
            }
        }

        return true;
    }

    private function isVisible(): bool
    {
        if ($this->visible === 'always') {
            return true;
        }
        if ($this->visible === 'never') {
            return false;
        }

        $todayStart = strtotime('today');
        $todayDateInt = (int) date('Ymd', $todayStart);

        $isAfterStart = true;
        if (!empty($this->visible_start_date)) {
                $startDateInt = (int) date('Ymd', $this->visible_start_date);
                $isAfterStart = $startDateInt <= $todayDateInt;
        }

        $isBeforeEnd = true;
        if (!empty($this->visible_end_date)) {
            $endDateString = date('Y-m-d', $this->visible_end_date) . ' 23:59:59';
            $visibleEndOfDay = strtotime($endDateString);
            $isBeforeEnd = $visibleEndOfDay >= $todayStart;
        }

        return $isAfterStart && $isBeforeEnd;
    }

    public function canEdit(User $user): bool
    {
        if ($user->id === $this->range_id) {
            return true;
        }

        return $GLOBALS['perm']->have_perm('root', $user->id) || $GLOBALS['perm']->have_studip_perm('tutor', $this->range_id, $user->id);
    }

    public function canEditContent(User $user): bool
    {
        if ($this->canEdit($user)) {
            return true;
        }
        if ($this->permission_scope === 'unit') {
            if ($this->permission_type === 'all' || $this->writable_all) {
                if ($this->isWritable()) {
                    return true;
                }
                return false;
            }
            if ($this->permission_type === 'users') {
                return in_array($user->id, json_decode($this->writable_approval)) && $this->isWritable();
            }
            if ($this->permission_type === 'groups') {
                $groups = json_decode($this->writable_approval);
                foreach ($groups as $groupId) {
                    $group = \Statusgruppen::find($groupId);
                    if ($group && $group->isMember($user->id)) {
                        return $this->isWritable();
                    }
                }
            }
        }

        return false;
    }

    private function isWritable(): bool
    {
        if ($this->writable === 'always') {
            return true;
        }
        if ($this->writable === 'never') {
            return false;
        }

        return
            (empty($this->writable_start_date) || $this->writable_start_date < strtotime('today'))
            && (empty($this->writable_end_date) || $this->writable_end_date >= strtotime('today'));
    }

    public function copy(
        User $user,
        string $rangeId,
        string $rangeType,
        array $modified = null,
        bool $duplicate = false
    ): Unit {
        $sourceUnitElement = $this->structural_element;

        $newElement = $sourceUnitElement->copyToRange($user, $rangeId, $rangeType, '', $duplicate);

        if ($modified !== null) {
            $newElement->title = $modified['title'] ?? $newElement->title;
            $newElement->payload['color'] = $modified['color'] ?? 'studip-blue';
            $newElement->payload['description'] = $modified['description'] ?? $newElement->payload['description'];
            $newElement->store();
        }

        $newUnit = \Courseware\Unit::build([
            'range_id' => $rangeId,
            'range_type' => $rangeType,
            'structural_element_id' => $newElement->id,
            'content_type' => 'courseware',
            'position' => $this->getNewPosition($rangeId),
            'creator_id' => $user->id,
            'public' => '',
            'permission_scope' => $duplicate ? $this->permission_scope : 'unit',
            'permission_type' => $duplicate ? $this->permission_type : 'all',
            'visible' => $duplicate ? $this->visible : 'always',
            'visible_all' => $duplicate ? $this->visible_all : 0,
            'visible_start_date' => $duplicate ? $this->visible_start_date : null,
            'visible_end_date' => $duplicate ? $this->visible_end_date : null,
            'visible_approval' => $duplicate ? $this->visible_approval : '',
            'writable' => $duplicate ? $this->writable : 'never',
            'writable_all' => $duplicate ? $this->writable_all : 0,
            'writable_start_date' => $duplicate ? $this->writable_start_date : null,
            'writable_end_date' => $duplicate ? $this->writable_end_date : null,
            'writable_approval' => $duplicate ? $this->writable_approval : '',
            'config' => $this->config,
        ]);

        $newUnit->store();

        return $newUnit;
    }
    /**
     * Export available data of a given user into a storage object
     * (an instance of the StoredUserData class) for that user.
     *
     * @param StoredUserData $storage object to store data into
     */
    public static function exportUserData(\StoredUserData $storage)
    {
        $units = \DBManager::get()->fetchAll(
            'SELECT * FROM cw_units WHERE creator_id = ?',
            [$storage->user_id]
        );
        if ($units) {
            $storage->addTabularData(_('Courseware Lernmaterialien'), 'cw_units', $units);
        }

    }

    public static function getNewPosition($range_id): int
    {
        return static::countBySQL('range_id = ?', [$range_id]);
    }

    public function updatePositionsAfterDelete(): void
    {
        if (is_null($this->position)) {
            return;
        }

        $db = \DBManager::get();
        $stmt = $db->prepare(
            sprintf(
                'UPDATE
              %s
            SET
              position = position - 1
            WHERE
              range_id = :range_id AND
              position > :position',
                'cw_units'
            )
        );
        $stmt->bindValue(':range_id', $this->range_id);
        $stmt->bindValue(':position', $this->position);
        $stmt->execute();
    }

    public static function updatePositions($range, $positions): void
    {
        $db = \DBManager::get();
        $query = sprintf(
            'UPDATE
                %s 
            SET
                position = FIND_IN_SET(id, ?) - 1
            WHERE
                range_id = ?',
            'cw_units'
        );
        $args = array(join(',', $positions), $range->id);
        $stmt = $db->prepare($query);
        $stmt->execute($args);
    }

    public function hasRootLayout()
    {
        return !isset($this->config['root_layout']) || $this->config['root_layout'] !== 'none';
    }

    public function findOrCreateFirstElement(): StructuralElement
    {
        if ($this->hasRootLayout()) {
            return $this->structural_element;
        }

        $children = $this->structural_element->children;
        if (count($children) > 0) {
            return $children[0];
        }

        $struct = StructuralElement::create([
            'parent_id' => $this->structural_element->id,
            'range_id' => $this->range_id,
            'range_type' => $this->range_type,
            'owner_id' => $this->structural_element->owner_id,
            'editor_id' => $this->structural_element->editor_id,
            'title' => _('neue Seite'),
        ]);


        return $struct;
    }

    public function getRangeCourseId(): string
    {
        return $this->range_id;
    }

    public function getRangeName(): string
    {
        return $this->structural_element->title;
    }

    public function getRangeIcon($role): string
    {
        return \Icon::create('content2', $role);
    }

    public function getRangeUrl(): string
    {
        if ($this->structural_element->range_type === 'user') {
            return 'contents/courseware/';
        }

        return 'course/courseware/' . '?cid=' . $this->range_id;
    }

    public function isRangeAccessible(string $user_id = null): bool
    {
        $user = \User::find($user_id);
        if ($user) {
            return $this->canRead($user);
        }

        return false;
    }

    public function getFeedbackElement()
    {
        return \FeedbackElement::findOneBySQL(
            'range_id = ? AND range_type = ?',
            [$this->id, self::class]
        );
    }

    public function getRange()
    {
        return $this->range_type::find($this->range_id);
    }
}
