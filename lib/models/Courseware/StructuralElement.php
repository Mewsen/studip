<?php

namespace Courseware;

use Courseware\Filesystem\PublicFolder;
use JSONArrayObject;
use User;

/**
 * Courseware's structural elements.
 *
 * @author  Marcus Eibrink-Lunzenauer <lunzenauer@elan-ev.de>
 * @author  Till Glöggler <gloeggler@elan-ev.de>
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 5.0
 *
 * @property int $id database column
 * @property int|null $parent_id database column
 * @property int $is_link database column
 * @property int|null $target_id database column
 * @property string $range_id database column
 * @property string|null $range_type database column
 * @property string $owner_id database column
 * @property string $editor_id database column
 * @property string|null $edit_blocker_id database column
 * @property int $position database column
 * @property string $title database column
 * @property string|null $image_id database column
 * @property string $image_type database column
 * @property string|null $purpose database column
 * @property \JSONArrayObject $payload database column
 * @property int $public database column
 * @property int $commentable database column
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
 * @property \JSONArrayObject $content_approval database column
 * @property \JSONArrayObject $copy_approval database column
 * @property \JSONArrayObject $external_relations database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \SimpleORMapCollection<StructuralElement> $children has_many StructuralElement
 * @property \SimpleORMapCollection<Container> $containers has_many Container
 * @property \SimpleORMapCollection<StructuralElementComment> $comments has_many StructuralElementComment
 * @property \SimpleORMapCollection<StructuralElementFeedback> $feedback has_many StructuralElementFeedback
 * @property StructuralElement|null $parent belongs_to StructuralElement
 * @property \User $user belongs_to \User
 * @property \Course $course belongs_to \Course
 * @property \User $owner belongs_to \User
 * @property \User $editor belongs_to \User
 * @property \User|null $edit_blocker belongs_to \User
 * @property Task $task has_one Task
 * @property mixed $image additional field
 */
class StructuralElement extends \SimpleORMap implements \PrivacyObject, \FeedbackRange
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'cw_structural_elements';

        $config['serialized_fields']['payload'] = JSONArrayObject::class;
        $config['serialized_fields']['visible_approval'] = JSONArrayObject::class;
        $config['serialized_fields']['writable_approval'] = JSONArrayObject::class;
        $config['serialized_fields']['content_approval'] = JSONArrayObject::class;
        $config['serialized_fields']['copy_approval'] = JSONArrayObject::class;
        $config['serialized_fields']['external_relations'] = JSONArrayObject::class;

        $config['has_many']['children'] = [
            'class_name' => StructuralElement::class,
            'assoc_foreign_key' => 'parent_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY position',
        ];

        $config['has_many']['containers'] = [
            'class_name' => Container::class,
            'assoc_foreign_key' => 'structural_element_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY position',
        ];

        $config['has_one']['task'] = [
            'class_name' => Task::class,
            'assoc_foreign_key' => 'structural_element_id',
            'on_delete' => 'delete',
        ];

        $config['belongs_to']['parent'] = [
            'class_name' => StructuralElement::class,
            'foreign_key' => 'parent_id',
        ];

        $config['belongs_to']['user'] = [
            'class_name' => \User::class,
            'foreign_key' => 'range_id',
            'assoc_foreign_key' => 'user_id',
        ];

        $config['belongs_to']['course'] = [
            'class_name' => \Course::class,
            'foreign_key' => 'range_id',
            'assoc_foreign_key' => 'seminar_id',
        ];

        $config['belongs_to']['owner'] = [
            'class_name' => User::class,
            'foreign_key' => 'owner_id',
        ];

        $config['belongs_to']['editor'] = [
            'class_name' => User::class,
            'foreign_key' => 'editor_id',
        ];

        $config['belongs_to']['edit_blocker'] = [
            'class_name' => User::class,
            'foreign_key' => 'edit_blocker_id',
        ];

        $config['has_many']['comments'] = [
            'class_name' => StructuralElementComment::class,
            'assoc_foreign_key' => 'structural_element_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY chdate',
        ];

        $config['has_many']['feedback'] = [
            'class_name' => StructuralElementFeedback::class,
            'assoc_foreign_key' => 'structural_element_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY chdate',
        ];

        $config['additional_fields']['image'] = [
            'get' => 'getImage',
            'set' => 'setImage'
        ];

        $config['registered_callbacks']['before_delete'][] = 'cbBeforeDelete';

        parent::configure($config);
    }

    public function cbBeforeDelete()
    {
        $image = $this->getImage();
        if (is_a($image, \FileRef::class)) {
            $image->delete();
        }
        \FeedbackElement::deleteBySQL('range_id = ? AND range_type = ?', [$this->id, self::class]);
    }

    /**
     * @return null|\FileRef|\StockImage
     */
    public function getImage()
    {
        if (!$this->image_id) {
            return null;
        }

        if (!in_array($this->image_type, [\FileRef::class, \StockImage::class])) {
            return null;
        }

        return $this->image_type::find($this->image_id);
    }

    /**
     * @param $image null|\FileRef|\StockImage
     */
    public function setImage($image): void
    {
        if (is_null($image)) {
            $this->image_id = null;
        } elseif (is_a($image, \FileRef::class)) {
            $this->image_id = $image->getId();
            $this->image_type = \FileRef::class;
        } elseif (is_a($image, \StockImage::class)) {
            $this->image_id = $image->getId();
            $this->image_type = \StockImage::class;
        } else {
            throw new \BadMethodCallException('Invalid argument to method ' . __METHOD__);
        }
    }

    /**
     * Returns the root element of a courseware instance for a user.
     *
     * @param string $userId the user's id to return the root element for
     *
     * @return ?StructuralElement null, if there is none, the root StructuralElement if there is one
     */
    public static function getCoursewareUser(string $userId): ?StructuralElement
    {
        return self::getCourseware($userId, 'user');
    }

    /**
     * Returns the root element of a courseware instance for a course.
     *
     * @param string $courseId the course's id to return the root element for
     *
     * @return ?StructuralElement null, if there is none, the root StructuralElement if there is one
     */
    public static function getCoursewareCourse(string $courseId): ?StructuralElement
    {
        return self::getCourseware($courseId, 'course');
    }

    public static function getSharedCoursewareUser(string $root_id): ?StructuralElement
    {
        return self::getCourseware('', '', $root_id);
    }

    private static function getCourseware(string $rangeId, string $rangeType, ?string $root_id = null): ?StructuralElement
    {
        if ($root_id) {
            $result = self::find($root_id);
        } else {
            $result = self::findOneBySQL(
                'range_id = ?
                AND range_type = ? AND parent_id IS NULL',
                [$rangeId, $rangeType]
            );
        }

        return $result;
    }

    /**
     * Returns the ID of the associated range.
     *
     * @return string the id of the range
     */
    public function getRangeId(): string
    {
        return $this->range_id;
    }

    /**
     * @return bool true, if this object is the root of a courseware, false otherwise
     */
    public function isRootNode(): bool
    {
        return null === $this->parent_id;
    }

    /**
     * @return bool true, if this object purpose is task, false otherwise
     */
    public function isTask(): bool
    {
        return $this->purpose === 'task';
    }

    /**
     * @param User $user the user to validate
     *
     * @return bool true if the user may edit this instance
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function canEdit($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($GLOBALS['perm']->have_perm('root', $user->id)) {
            return true;
        }

        switch ($this->range_type) {
            case 'user':
                if ($this->range_id === $user->id) {
                    return true;
                }

                return $this->hasWriteContentApproval($user);

            case 'course':
                $unit = $this->findUnit();
                if ($unit->permission_scope === 'unit' && !$this->isTask()) {
                    return $unit->canEditContent($user);
                } else {
                    $hasEditingPermission = $this->hasEditingPermission($user, $unit);
                    if ($this->isTask()) {
                        $task = $this->task;
                        if (!$task) {
                            $task = $this->findParentTask();
                            if (!$task) {
                                return false;
                            }
                        }

                        if ($hasEditingPermission) {
                            return false;
                        }

                        if ($task->isSubmitted()) {
                            return false;
                        }

                        return $task->userIsASolver($user);
                    }

                    if ($hasEditingPermission) {
                        return true;
                    }

                    return $this->hasWriteApproval($user);
                }

            default:
                throw new \InvalidArgumentException('Unknown range type.');
        }
    }

    /**
     * @param mixed $user the user to validate
     *
     * @return bool true if the user may read this instance
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function canRead($user): bool
    {
        // root darf immer
        if ($user && $GLOBALS['perm']->have_perm('root', $user->id)) {
            return true;
        }

        switch ($this->range_type) {
            case 'user':
                if ($user && $this->range_id === $user->id) {
                    return true;
                }

                $link = StructuralElement::findOneBySQL('target_id = ?', [$this->id]);
                if ($link) {
                    return true;
                }

                return $this->hasReadContentApproval($user);
            case 'course':
                $unit = $this->findUnit();
                if ($unit->permission_scope === 'unit') {
                    return $unit->canRead($user);
                } else {
                    if (!$user || !$GLOBALS['perm']->have_studip_perm('user', $this->range_id, $user->id)) {
                        return false;
                    }

                    if ($this->canEdit($user)) {
                        return true;
                    }

                    return $this->hasReadApproval($user);
                }

            default:
                throw new \InvalidArgumentException('Unknown range type.');
        }
    }

    public function canVisit($user): bool
    {
        // root darf immer
        if ($user && $GLOBALS['perm']->have_perm('root', $user->id)) {
            return true;
        }

        switch ($this->range_type) {
            case 'user':
                if ($user && $this->range_id === $user->id) {
                    return true;
                }

                return $this->hasReadContentApproval($user);

            case 'course':
                $unit = $this->findUnit();
                if ($unit->permission_scope === 'unit') {
                    return $unit->canRead($user);
                } else {
                    if (!$user || !$GLOBALS['perm']->have_studip_perm('user', $this->range_id, $user->id)) {
                        return false;
                    }

                    if ($this->isTask()) {
                        $task = $this->task;
                        if (!$task) {
                            $task = $this->findParentTask();
                            if (!$task) {
                                return false;
                            }
                        }

                        if ($task->isSubmitted() && $this->hasEditingPermission($user)) {
                            return true;
                        }

                        if ($task->isSubmitted()) {
                            if ($task->visible) {
                                return true;
                            }
                            $solvers = $task->getTaskGroup()->getSolvers();
                            foreach ($solvers as $solver) {
                                if ($solver->id === $user->id) {
                                    return true;
                                }
                            }
                            return false;
                        }

                        return $task->userIsASolver($user);
                        // TODO (mel): Das ist die ursprüngliche Variante, die aber jetzt kompliziert ist. Mit Nico sprechen!
                        // return $task->userIsASolver($user) || $task->userIsAPeerReviewer($user);
                    }

                    if ($this->canEdit($user)) {
                        return true;
                    }

                    return $this->hasReadApproval($user) && $this->canReadSequential($user);
                }

            default:
                throw new \InvalidArgumentException('Unknown range type.');
        }
    }

    /**
     * @param \User|\Seminar_User $user
     */
    public function hasEditingPermission(User $user, ?Unit $unit = null): bool
    {
        if (!isset($unit)) {
            $unit = $this->findUnit();
        }
        return $GLOBALS['perm']->have_perm('root', $user->id)
            || $GLOBALS['perm']->have_studip_perm(
                $unit->config['editing_permission'] ?? 'tutor',
                $this->range_id,
                $user->id
            );
    }

    private function hasReadApproval($user): bool
    {
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

        return false;
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

    private function hasReadContentApproval($user): bool
    {
        if (count($this->content_approval) === 0) {
            if ($this->isRootNode()) {
                return false;
            }
            return $this->parent->hasReadContentApproval($user);
        }

        // find user in users
        $users = $this->content_approval['users'];
        foreach ($users as $listedUserPerm) {
            if (!empty($listedUserPerm['expiry']) && strtotime($listedUserPerm['expiry']) < strtotime('today')) {
                if ($this->isRootNode()) {
                    return false;
                }
                return $this->parent->hasReadContentApproval($user);
            }
            if ($listedUserPerm['id'] == $user->id && $listedUserPerm['read'] == true) {
                return true;
            }
        }
        //User not found.
        return false;
    }

    private function hasWriteApproval($user): bool
    {
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

    private function hasWriteContentApproval($user): bool
    {
        if (!count($this->content_approval)) {
            if ($this->isRootNode()) {
                return false;
            }
            return $this->parent->hasWriteContentApproval($user);
        }

        // find user in users
        $users = $this->content_approval['users'];
        foreach ($users as $listedUserPerm) {
            if (!empty($listedUserPerm['expiry']) && strtotime($listedUserPerm['expiry']) < strtotime('today')) {
                if ($this->isRootNode()) {
                    return false;
                }
                return $this->parent->hasWriteContentApproval($user);
            }
            if ($listedUserPerm['id'] == $user->id && $listedUserPerm['write']) {
                return true;
            }
        }

        if ($this->isRootNode()) {
            return false;
        }
        return $this->parent->hasWriteContentApproval($user);
    }

    /**
     * @param mixed $user the user to validate
     *
     * @return bool true if the user may read this instance in sequential progression
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function canReadSequential($user): bool
    {
        $unit = $this->findUnit();
        if (!$unit->config['sequential_progression']) {
            return true;
        }

        return $this->previousProgressAchieved($user);
    }

    /**
     * @param mixed $user the user to validate
     *
     * @return bool true if the user has achieved previous elements
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function previousProgressAchieved($user): bool
    {
        $elements = $this->findCoursewareElements($user);

        foreach ($elements as $element) {
            // found me in depth-first order
            // so everything before me was fine and we're done
            if ($element->id == $this->id) {
                break;
            }

            if (!$element->hasBeenAchieved($user)) {
                return false;
            }
        }

        return true;
    }

    private function findCoursewareElements($user): array
    {
        $unit = $this->findUnit();
        $root = $unit->structural_element;
        $elements = array_merge([$root], $root->findDescendants($user));

        return $elements;
    }

    private function hasBeenAchieved($user): bool
    {
        foreach ($this->containers as $container) {
            foreach ($container->blocks as $block) {
                /** @var ?UserProgress $progress */
                $progress = UserProgress::findOneBySQL('user_id = ? and block_id = ?', [$user->id, $block->id]);

                if (!$progress || $progress->grade != 1) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns all projects of a user and a given purpose.
     *
     * @param string $userId  the ID of the user
     * @param string $purpose a string containing the purpose of the projects
     *
     * @return StructuralElement[] a list of projects
     */
    public static function findProjects(string $userId, string $purpose = 'all'): array
    {
        $root = self::getCoursewareUser($userId);
        if ('all' == $purpose) {
            return self::findBySQL('range_id = ? AND parent_id = ? ORDER BY position ASC', [$userId, $root->id]);
        } else {
            return self::findBySQL('range_id = ? AND parent_id = ? AND purpose = ? ORDER BY position ASC', [
                $userId,
                $root->id,
                $purpose,
            ]);
        }
    }

    /**
     * Return the number of children of this instance.
     *
     * @return int the number of children
     */
    public function countChildren(): int
    {
        return self::countBySQL('parent_id= ? ', [$this->id]);
    }

    /**
     * Returns the list of all ancestors traversing up to the root.
     *
     * @return array a list of all ancestors of this instance up to the root
     */
    public function findAncestors(): array
    {
        $ancestors = [];

        if ($this->parent) {
            $ancestors[] = $this->parent;
            $ancestors = array_merge($this->parent->findAncestors(), $ancestors);
        }

        return $ancestors;
    }

    public function findUnit()
    {
        if ($this->isRootNode()) {
            $root = $this;
        } else {
            $root = $this->findAncestors()[0];
        }

        return Unit::findOneBySQL('structural_element_id = ?', [$root->id]);
    }

    /**
     * Returns the list of all descendants of this instance in depth-first search order.
     *
     * @param ?User  $user  the user whose bookmarked structural elements will be returned
     *
     * @return StructuralElement[] a list of all descendants
     */
    public function findDescendants(?User $user = null)
    {
        $descendants = [];
        foreach ($this->children as $child) {
            if ($user === null || $child->canRead($user)) {
                $descendants[] = $child;
                $descendants = array_merge($descendants, $child->findDescendants($user));
            }
        }

        return $descendants;
    }

    /**
     * Creates a new and empty courseware instance for a given range.
     *
     * @param string $rangeId   the ID of the range
     * @param string $rangeType the type of the range
     *
     * @return Instance the created courseware instance
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function createEmptyCourseware(string $rangeId, string $rangeType): Instance
    {
        if ('user' == $rangeType) {
            $user = \User::find($rangeId);
        } else {
            /** @var ?\Course $course */
            $course = \Course::find($rangeId);
            /** @var ?\User $user */
            $user = \User::find($GLOBALS['user']->id); //must be dozent
            if ('dozent' != $course->getParticipantStatus($user->id)) {
                $coursemembers = $course->getMembersWithStatus('dozent'); //get studip perm
                $user = $coursemembers[0]->user;
            }
        }

        $struct = self::build([
            'parent_id' => null,
            'range_id' => $rangeId,
            'range_type' => $rangeType,
            'owner_id' => $user->id,
            'editor_id' => $user->id,
            'title' => _('Neue Seite'),
            'commentable' => 0
        ]);

        $struct->store();

        return new Instance($struct);
    }

    /**
     * Counts and returns the number of containers in this structural element.
     *
     * @return int the number of containers of this structural element
     */
    public function countContainers(): int
    {
        return Container::countBySql('structural_element_id = ?', [$this->id]);
    }

    /**
     * Returns all structural elements that a user bookmarked in a range.
     *
     * @param User   $user  the user whose bookmarked structural elements will be returned
     * @param \Range $range the range in which the user bookmarked structural elements
     *
     * @return array a list of bookmarked structural elements
     */
    public static function findUsersBookmarksByRange(User $user, \Range $range): array
    {
        if (!in_array($range->getRangeType(), ['course', 'user'])) {
            throw new \InvalidArgumentException();
        }

        $sql = <<<'SQL'
            SELECT s.* FROM cw_structural_elements s
            JOIN cw_bookmarks b
            ON s.id = b.element_id
            WHERE s.range_id = ? AND s.range_type = ? AND b.user_id = ?
            ORDER BY b.chdate DESC
SQL;
        $params = [$range->getRangeId(), $range->getRangeType(), $user->id];

        return \DBManager::get()->fetchAll($sql, $params, StructuralElement::class . '::buildExisting');
    }

    /**
     * Returns the URL of the image associated to this structural element.
     *
     * @return string|null the image URL, if it exists
     */
    public function getImageUrl()
    {
        $image = $this->getImage();
        if ($image) {
            if (is_a($image, \FileRef::class)) {
                return $image->getDownloadURL();
            } elseif (is_a($image, \StockImage::class)) {
                return $image->getDownloadURL(\StockImage::SIZE_SMALL);
            }
        }

        return null;
    }

    protected function copyBase(
        User $user,
        ?StructuralElement $parent = null,
        ?string $rangeId = null,
        ?string $rangeType = null,
        string $purpose = '',
        bool $duplicate = false,
        array &$mapping = [],
    ): StructuralElement {
        $parentId = $parent ? $parent->id : null;
        $rangeId = $rangeId ?? ($parent ? $parent->range_id : null);
        $rangeType = $rangeType ?? ($parent ? $parent->range_type : null);
        $position = $parent ? $parent->countChildren() : 0;

        $element = self::build([
            'parent_id' => $parentId,
            'range_id' => $rangeId,
            'range_type' => $rangeType,
            'owner_id' => $user->id,
            'editor_id' => $user->id,
            'edit_blocker_id' => null,
            'title' => $this->title,
            'purpose' => $purpose ?: $this->purpose,
            'position' => $position,
            'payload' => $this->payload,
            'commentable' => $duplicate ? $this->commentable : 0,
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
            'content_approval' => $duplicate ? $this->content_approval : '',
            'image_id' => null,
            'image_type' => $this->image_type,
        ]);

        $element->store();

        $element->image_id = $this->copyImage($user, $element);
        $element->store();
        
        $mapping['elements'][$this->id] = $element->id;
        $this->copyContainers($user, $element, $mapping);
        $this->copyChildren($user, $element, $purpose,  $duplicate, $mapping);

        return $element;
    }

    /**
     * Copies this instance into another course oder users contents.
     *
     * @param User $user   this user will be the owner of the copy
     * @param \Range $parent the target where to copy this instance
     *
     * @return StructuralElement the copy of this instance
     */


    public function copyToRange(
        User $user,
        string $rangeId,
        string $rangeType,
        string $purpose = '',
        bool $duplicate = false,
        array &$mapping = [],
    ): StructuralElement {
        return $this->copyBase($user, null, $rangeId, $rangeType, $purpose, $duplicate, $mapping);
    }

    /**
     * Copies this instance as a child into another structural element.
     *
     * @param User $user   this user will be the owner of the copy
     * @param StructuralElement $parent the target where to copy this instance
     * @param string $purpose the purpose of copying this instance
     * @param string $recursiveId the optional mapping id for copying child structural elements upon recursive call to this function
     *
     * @return StructuralElement the copy of this instance
     */
    public function copy(
        User $user,
        StructuralElement $parent,
        string $purpose = '',
        array &$mapping = [],
        bool $duplicate = false
    ): StructuralElement {
        $ancestorIds = array_column($parent->findAncestors(), 'id');
        $ancestorIds[] = $parent->id;
        if (in_array($this->id, $ancestorIds)) {
            throw new \InvalidArgumentException('Cannot copy into descendants.');
        }
        return $this->copyBase($user, $parent, null, null, $purpose, $duplicate, $mapping);
    }

    private function copyImage(User $user, StructuralElement $element) : ?string
    {
        if ($this->image_type === \StockImage::class) {
            return $this->image_id;
        }

        if ($this->image_type === \FileRef::class) {
            $file_ref_id = null;

            /** @var ?\FileRef $original_file_ref */
            $original_file_ref = \FileRef::find($this->image_id);
            if ($original_file_ref) {
                $instance = new Instance($this->getCourseware($element->range_id, $element->range_type));
                $folder = \Folder::findTopFolder($instance->getRoot()->id, PublicFolder::class, 'courseware');
                /** @var \FileRef $file_ref */
                $file_ref = \FileManager::copyFile($original_file_ref->getFileType(), $folder->getTypedFolder(), $user);
                $file_ref_id = $file_ref->id;
            }

            return $file_ref_id;
        }

        return null;
    }

    public function merge(User $user, StructuralElement $target): StructuralElement
    {
        // merge with target
        if (!$target->image_id) {
            $target->image_id = $this->copyImage($user, $target);
            $target->image_type = $this->image_type;
        }

        if ($target->title === 'Neue Seite' || $target->title === 'New page') {
            $target->title = $this->title;
        }

        if (!$target->purpose) {
            $target->purpose = $this->purpose;
        }

        if (!$target->payload['color']) {
            $target->payload['color'] = $this->payload['color'];
        }

        if (!$target->payload['description']) {
            $target->payload['description'] = $this->payload['description'];
        }

        if (!$target->payload['license_type']) {
            $target->payload['license_type'] = $this->payload['license_type'];
        }

        if (!$target->payload['required_time']) {
            $target->payload['required_time'] = $this->payload['required_time'];
        }

        if (!$target->payload['difficulty_start']) {
            $target->payload['difficulty_start'] = $this->payload['difficulty_start'];
        }

        if (!$target->payload['difficulty_end']) {
            $target->payload['difficulty_end'] = $this->payload['difficulty_end'];
        }

        $target->store();

        // add Containers to target
        $this->copyContainers($user, $target);

        // copy Children
        $this->copyChildren($user, $target);

        return $this;
    }

    private function copyContainers(User $user, StructuralElement $newElement, array &$mapping = [])
    {
        foreach ($this->containers as $container) {
            [$newContainer, $blockMapsObjs] = $container->copy($user, $newElement);
            $mapping['containers'][$container->id] = $newContainer->id;
            $mapping['blocks'] = array_merge($mapping['blocks'] ?? [], $blockMapsObjs);
        }
    }

    private function copyChildren(
        User $user,
        StructuralElement $newElement,
        string $purpose = '',
        bool $duplicate = false,
        array &$mapping = []
    ): void {
        foreach ($this->children as $child) {
            $child->copy($user, $newElement, $purpose, $mapping, $duplicate);
        }
    }

    public function link(User $user, StructuralElement $parent): StructuralElement
    {
        $element = self::build([
            'parent_id' => $parent->id,
            'is_link' => 1,
            'target_id' => $this->id,
            'range_id' => $parent->range_id,
            'range_type' => $parent->range_type,
            'owner_id' => $user->id,
            'editor_id' => $user->id,
            'edit_blocker_id' => null,
            'title' => $this->title,
            'purpose' => $this->purpose,
            'position' => $parent->countChildren(),
            'payload' => $this->payload,
            'permission_type'=> $parent->permission_type,
            'visible' => $parent->visible,
            'visible_start_date' => $parent->visible_start_date,
            'visible_end_date' => $parent->visible_end_date,
            'writable' => $parent->writable,
            'writable_start_date' => $parent->writable_start_date,
            'writable_end_date' => $parent->writable_end_date,
            'visible_approval' => $parent->visible_approval,
            'writable_approval' => $parent->writable_approval,
            'content_approval' => $parent->content_approval,
            'commentable' => 0
        ]);

        $element->store();

        $this->linkChildren($user, $element);

        return $element;
    }

    private function linkChildren(User $user, StructuralElement $newElement): void
    {
        foreach ($this->children as $child) {
            $child->link($user, $newElement);
        }
    }

    public function pdfExport($user, bool $with_children = false)
    {
        $doc = new \ExportPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $doc->setHeaderTitle(_('Courseware'));
        if ($this->course) {
            $doc->setHeaderTitle(sprintf(_('Courseware aus %s'), $this->course->name));
        }
        if ($this->user) {
            $doc->setHeaderTitle(sprintf(_('Courseware von %s'), $this->user->getFullName()));
        }

        if (!self::canVisit($user)) {
            $doc->addPage();
            $doc->addContent(_('Diese Seite steht Ihnen nicht zur Verfügung!'));

            return $doc;
        }

        $this->getElementPdfExport(0, $with_children, $user, $doc);

        if ($with_children) {
            $doc->addTOCPage();
            $doc->SetFont('helvetica', 'B', 16);
            $doc->MultiCell(0, 0, _('Inhaltsverzeichnis'), 0, 'C', 0, 1, '', '', true, 0);
            $doc->Ln();
            $doc->SetFont('helvetica', '', 12);
            $doc->addTOC(1, 'helvetica', '.', _('Inhaltsverzeichnis'), 'B', array(0,0,0));
            $doc->endTOCPage();
        }


        return $doc;
    }

    private function getElementPdfExport(int $depth, bool $with_children, $user, $doc)
    {
        if (!$this->canVisit($user)) {
            return '';
        }
        $doc->addPage();
        $doc->Bookmark($this->title, $depth, 0, '', '', array(128,0,0));
        $html = "<h1>" . htmlReady($this->title) . "</h1>";
        $html .= $this->getContainerPdfExport();
        $doc->writeHTML($html);

        if ($with_children) {
            $this->getChildrenPdfExport($depth, $with_children, $user, $doc);
        }
    }

    private function getChildrenPdfExport(int $depth, bool $with_children, $user, $doc)
    {
        $depth++;
        foreach ($this->children as $child) {
            $child->getElementPdfExport($depth, $with_children, $user, $doc);
        }
    }

    private function getContainerPdfExport()
    {
        $containers = \Courseware\Container::findBySQL('structural_element_id = ?', [$this->id]);

        $html = '';
        foreach ($containers as $container) {
            $container_html_template = $container->type->getPdfHtmlTemplate();
            $html .= $container_html_template ? $container_html_template->render() : '';
        }

        return $html;
    }

    private function findParentTask()
    {
        if ($this->isRootNode()) {
            return null;
        }
        if ($this->task) {
            return $this->task;
        }

        return $this->parent->findParentTask();
    }

    private function performMapping($mapping)
    {
        // Blocks mapping.
        foreach ($mapping['blocks'] as $oldBlockId => $newBlockObj) {
            if ($newBlockObj->type->getType() === \Courseware\BlockTypes\Link::getType()) {
                $payload = $newBlockObj->type->getPayload();
                if ($payload['type'] === 'internal' && '' != $payload['target']) {
                    if (in_array($payload['target'], array_keys($mapping['elements']))) {
                        $payload['target'] = $mapping['elements'][intval($payload['target'])];
                    } else {
                        $payload['target'] = '';
                    }
                    $newBlockObj->type->setPayload($payload);
                    $newBlockObj->store();
                }
            }
        }
    }

    /**
     * Export available data of a given user into a storage object
     * (an instance of the StoredUserData class) for that user.
     *
     * @param StoredUserData $storage object to store data into
     */
    public static function exportUserData(\StoredUserData $storage)
    {
        $structuralElements = \DBManager::get()->fetchAll(
            'SELECT * FROM cw_structural_elements WHERE ? IN (owner_id, editor_id, range_id)',
            [$storage->user_id]
        );
        if ($structuralElements) {
            $storage->addTabularData(_('Courseware Seiten'), 'cw_structural_elements', $structuralElements);
        }

    }

    public function createChildrenFromCourseTopics()
    {
        if ($this->range_type === 'user') {
            return null;
        }
        $topics = \CourseTopic::findBySeminar_id($this->course->id);
        foreach($topics as $key => $topic) {
            self::create([
                'parent_id' => $this->id,
                'range_id' => $this->range_id,
                'range_type' => $this->range_type,
                'owner_id' => $this->owner_id,
                'editor_id' => $this->editor_id,
                'edit_blocker_id' => '',
                'title' => $topic->title,
                'purpose' => $this->purpose,
                'payload' => '',
                'position' => $key
            ]);
        }
    }

    public function getRangeCourseId(): string
    {
        return $this->range_id;
    }

    public function getRangeName(): string
    {
        return $this->title;
    }

    public function getRangeIcon($role): string
    {
        return \Icon::create('courseware', $role);
    }

    public function getRangeUrl(): string
    {
        $unit = $this->findUnit();

        if ($this->range_type === 'user') {
            return 'contents/courseware/courseware/' . $unit->id . '#/structural_element/' . $this->id;
        }

        return 'course/courseware/courseware/' . $unit->id . '?cid=' . $this->range_id . '#/structural_element/' . $this->id;
    }

    public function isRangeAccessible(?string $user_id = null): bool
    {
        $user =  \User::find($user_id);
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

    public function isTaskVisible(): bool
    {
        return $this->payload['task-visibility'];
    }
}
