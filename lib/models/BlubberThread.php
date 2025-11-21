<?php
/**
 * BlubberThread
 * Model class for BlubberThreads
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Rasmus Fuhse <fuhse@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       4.5
 *
 * @property string $id alias column for thread_id
 * @property string $thread_id database column
 * @property string $context_type database column
 * @property string $context_id database column
 * @property string $user_id database column
 * @property int $external_contact database column
 * @property string|null $content database column
 * @property string|null $display_class database column
 * @property int $visible_in_stream database column
 * @property int $commentable database column
 * @property JSONArrayObject|null $metadata database column
 * @property int|null $chdate database column
 * @property int|null $mkdate database column
 * @property SimpleORMapCollection<BlubberComment> $comments has_many BlubberComment
 * @property SimpleORMapCollection<BlubberParticipation> $participations has_many BlubberParticipation
 * @property SimpleORMapCollection<ObjectUserVisit> $visits has_many ObjectUserVisit
 * @property User $user belongs_to User
 */

class BlubberThread extends SimpleORMap implements PrivacyObject
{
    /** @var string the private context/range type */
    const CTX_TYPE_PRIVATE = 'private';

    /** @var string the public context/range type */
    const CTX_TYPE_PUBLIC = 'public';

    /** @var string the course context/range type */
    const CTX_TYPE_COURSE = 'course';

    /** @var string the institute context/range type */
    const CTX_TYPE_INST = 'institute';

    /** @var array the list of allowed context/range types */
    const ALLOWED_CTX_TYPES = [
        self::CTX_TYPE_PRIVATE,
        self::CTX_TYPE_PUBLIC,
        self::CTX_TYPE_COURSE,
        self::CTX_TYPE_INST,
    ];

    protected array $already_notified_user_ids = [];
    /**
     * Configures this model.
     *
     * @param array $config Configuration array
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'blubber_threads';

        $config['has_many']['comments'] = [
            'class_name' => BlubberComment::class,
            'on_store'   => 'store',
            'on_delete'  => 'delete',
            'order_by'   => 'ORDER BY mkdate ASC'
        ];
        $config['has_many']['participations'] = [
            'class_name' => BlubberParticipation::class,
            'on_store'   => 'store',
            'on_delete'  => 'delete',
        ];
        $config['belongs_to']['user'] = [
            'class_name'        => User::class,
            'foreign_key'       => 'user_id',
            'assoc_foreign_key' => 'user_id',
        ];
        $config['has_many']['visits'] = [
            'class_name'        => ObjectUserVisit::class,
            'assoc_foreign_key' => 'object_id',
            'on_delete'         => 'delete',
        ];
        $config['belongs_to']['parentthread'] = [
            'class_name'  => BlubberThread::class,
            'foreign_key' => 'parent_id',
        ];
        $config['has_many']['subthreads'] = [
            'class_name'        => BlubberThread::class,
            'assoc_foreign_key' => 'parent_id',
            'on_delete'         => 'delete',
            'on_store'          => 'store',
            'order_by'          => 'ORDER BY mkdate DESC'
        ];

        $config['serialized_fields']['metadata'] = JSONArrayObject::class;

        parent::configure($config);
    }

    /**
     * Checks if the Thread is of a specific type.
     *
     * @param string|array $types the types to check against, could be string or array of strings
     *
     * @return bool whether the type matches!
     * @throws Exception when the type in not allowed.
     */
    public function isOfContextType(mixed $types): bool
    {
        // Accept single string or an array of types
        if (is_string($types)) {
            $types = [$types];
        }

        $types = array_values(array_filter((array) $types));

        // No types provided -> invalid
        if (empty($types)) {
            throw new Exception('Undefined Blubber context type.');
        }

        // Check that all provided types are allowed
        $invalid = array_diff($types, self::ALLOWED_CTX_TYPES);
        if (!empty($invalid)) {
            throw new Exception('Undefined Blubber context type: ' . implode(', ', $invalid));
        }

        return in_array($this->context_type, $types, true);
    }

    /**
     * Recognizes lookups in blubber conversations as @username or @"Firstname lastname"
     * and turns them into usual studip-links.
     *
     * When the room is private and the user does not belong in the conversation, only a user-link is returned.
     * When the a user is called and is participating in that conversation, we notify him/her with special personal notification.
     *
     * In each case the @ will be converted to user-link if user has been found.
     *
     * @param array $matches
     * @return string
     */
    public function atSignLookups($matches)
    {
        $user_lookup = stripslashes(mb_substr($matches[0], 1));
        $user = null;
        if (!str_starts_with($user_lookup, '"')) {
            $user = User::findByUsername($user_lookup);
        } else {
            $user_fullname = mb_substr($user_lookup, 1, -1); // Strip quotes
            $user = User::findOneBySQL("CONCAT(Vorname, ' ', Nachname) = ?", [$user_fullname]);
        }
        if (!empty($user)
            && !$this->isNew()
            && $user->getId()
            && $user->getId() !== $GLOBALS['user']->id
        ) {
            if ($this->isOfContextType(self::CTX_TYPE_PUBLIC)
                || BlubberParticipation::userParticipatesIn($this->getId(), $user->getId())
                || $GLOBALS['perm']->have_perm('admin', $user->getId())) {
                $user_avatar = Avatar::getAvatar($GLOBALS['user']->id);
                $notification_avatar = Icon::create('blubber');
                if ($user_avatar->is_customized()) {
                    $notification_avatar = $user_avatar->getURL(Avatar::MEDIUM);
                }
                PersonalNotifications::add(
                    $user->getId(),
                    $this->getURL(),
                    sprintf(_('%s hat Sie in %s Chat Raum erwähnt.'), get_fullname(), $this->getName()),
                    'blubberthread_' . $this->getId(),
                    $notification_avatar,
                    true
                );
                // We record this notification here, in order to avoid redundancy!
                if (!in_array($user->getId(), $this->already_notified_user_ids)) {
                    $this->already_notified_user_ids[] = $user->getId();
                }
            }

            $oldbase = URLHelper::setBaseURL($GLOBALS['ABSOLUTE_URI_STUDIP']);
            $url = URLHelper::getLink('dispatch.php/profile', ['username' => $user->username]);
            URLHelper::setBaseURL($oldbase);

            return '[' . $user->getFullName() . ']' . $url . ' ';
        }

        return $matches[0];
    }

    /**
     * @return BlubberThread[]
     */
    public static function findBySQL($sql, $params = [])
    {
        return parent::findAndMapBySQL(function ($thread) {
            return self::upgradeThread($thread);
        }, $sql, $params);
    }

    /**
     * @return BlubberThread|null
     */
    public static function find($id)
    {
        return self::upgradeThread(parent::find($id));
    }

    /**
     * Checks if a BlubberThread has a display_class and returns an instance of
     * display_class with the same data. Otherwise returns BlubberThread.
     * @param BlubberThread|boolean $thread : instance of BlubberThread or false
     * @return BlubberThread|boolean
     */
    public static function upgradeThread($thread)
    {
        if ($thread
            && $thread['display_class']
            && $thread['display_class'] !== 'BlubberThread'
            && is_subclass_of($thread['display_class'], 'BlubberThread')
        ) {
            $class = $thread['display_class'];
            $display_thread = $class::buildExisting($thread->toRawArray());
            return $display_thread;
        }

        return $thread;
    }

    /**
     * @param string $limit      optional; limits the number of results
     * @param string $since      optional; selects threads after this date (exclusive)
     * @param string $olderthan  optional; selects threads before this date (exclusive)
     * @param string $user_id    optional; use this ID instead of $GLOBALS['user']->id
     * @param string $search     optional; filters the threads by a search string
     *
     * @return array  an array of the user's global BlubberThreads
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function findMyGlobalThreads($limit = 51, $since = null, $olderthan = null, string $user_id = null, $search = null)
    {
        $user_id = $user_id ?? $GLOBALS['user']->id;

        $condition =
                    "LEFT JOIN blubber_comments
                        ON blubber_comments.thread_id = blubber_threads.thread_id
                    WHERE (blubber_threads.content IS NULL OR blubber_threads.content = '')
                        AND blubber_comments.comment_id IS NULL
                        AND (display_class IS NULL OR display_class = 'BlubberThread')
                        AND UNIX_TIMESTAMP() - blubber_threads.mkdate > 60 * 60";
        self::deleteBySQL($condition);

        $union = new SQLUnionQuery();
        $union->setUnionAll(true);

        // Public, global and participation.
        $query = new SQLQuery('blubber_threads');
        $query->where('visible_in_stream = 1');
        if ($GLOBALS['perm']->have_perm('root')) {
            $query->where('context_type', "context_type = :context_type", [':context_type' => self::CTX_TYPE_PUBLIC]);
        } else {
            $query->where(
                'context_type',
                "context_type IN (:context_type)",
                [':context_type' => [self::CTX_TYPE_PUBLIC, self::CTX_TYPE_COURSE, self::CTX_TYPE_INST]]
            );

        }
        $query->where(
            'public/global/participations',
            implode(' OR ', [
                "blubber_threads.thread_id = 'global'",
                "user_id = :user_id",
                "thread_id IN (SELECT thread_id FROM blubber_comments WHERE user_id = :user_id)"
            ]),
            [':user_id' => $user_id]
        );
        $union->add($query);

        // Courses
        $course_ids = self::getMyBlubberCourses($user_id);
        if (count($course_ids) > 0) {
            $query = new SQLQuery('blubber_threads');
            $query->where('visible_in_stream = 1');
            $query->where('inst_type', "context_type = '" . self::CTX_TYPE_COURSE . "'");
            $query->where('inst_ids', 'context_id IN (:course_ids)', [':course_ids' => $course_ids]);
            $union->add($query);
        }

        // Institutes
        $institute_ids = self::getMyBlubberInstitutes($user_id);
        if (count($institute_ids) > 0) {
            $query = new SQLQuery('blubber_threads');
            $query->where('visible_in_stream = 1');
            $query->where('inst_type', "context_type = '" . self::CTX_TYPE_INST . "'");
            $query->where('inst_ids', 'context_id IN (:institute_ids)', [':institute_ids' => $institute_ids]);
            $union->add($query);
        }

        // Private Room Participating.
        $query = new SQLQuery('blubber_threads');
        $query->join('blubber_participations', 'blubber_participations', 'blubber_participations.thread_id = blubber_threads.thread_id', 'JOIN');
        $query->where("context_type = '" . self::CTX_TYPE_PRIVATE . "'");
        $query->where('visible_in_stream = 1');
        $query->where('user', 'blubber_participations.user_id = :user_id', [':user_id' => $user_id]);
        $query->where('blubber_participations.external_contact = 0');

        $union->add($query);

        $thread_ids = $union->fetchFirst();

        $threads = [];

        do {
            [$newthreads, $filtered, $new_since, $new_olderthan] = self::getOrderedThreads(
                $thread_ids,
                $limit - count($threads),
                $since,
                $olderthan,
                $user_id,
                $search
            );

            if ($since) {
                $since = max($since, $new_since);
            }
            if ($olderthan) {
                $olderthan = min($olderthan, $new_olderthan);
            } else {
                $olderthan = $new_olderthan;
            }
            $threads = array_merge($threads, $newthreads);
        } while ($filtered && $limit);

        return $threads;
    }

    /**
     * This method is used to get the ordered (upgraded) threads. Because a thread is also able to
     * manage its own visibility and not only pure SQL, we need to execute
     * @param $thread_ids
     * @param string $limit      optional; limits the number of results
     * @param string $since      optional; selects threads after this date (exclusive)
     * @param string $olderthan  optional; selects threads before this date (exclusive)
     * @param string $user_id    optional; use this ID instead of $GLOBALS['user']->id
     * @param string $search     optional; filters the threads by a search string
     * @return array
     */
    protected static function getOrderedThreads($thread_ids, $limit = 51, $since = null, $olderthan = null, string $user_id = null, $search = null)
    {
        $query = SQLQuery::table('blubber_threads')->join(
            'blubber_comments',
            'blubber_comments', 'blubber_threads.thread_id = blubber_comments.thread_id',
            'LEFT JOIN'
        );

        $query->where(
            "filter_thread_ids",
            "blubber_threads.thread_id IN (:thread_ids)",
            ['thread_ids' => $thread_ids]
        );
        if ($search !== null) {
            $query->where(
                "search",
                "(blubber_threads.content LIKE :search OR blubber_comments.content LIKE :search)",
                ['search' => '%' . $search . '%']
            );
        }
        if ($since !== null) {
            $query->where(
                'since',
                '(blubber_comments.mkdate > :since OR blubber_threads.mkdate > :since)',
                compact('since')
            );
        }
        $query->groupBy('blubber_threads.thread_id');
        if ($olderthan !== null) {
            $query->having(
                'olderthan',
                "IFNULL(MAX(blubber_comments.mkdate), blubber_threads.mkdate) < :olderthan",
                ['olderthan' => $olderthan]
            );
        }
        $query->orderBy("MAX(blubber_comments.mkdate) DESC, blubber_threads.mkdate DESC");
        $query->limit($limit);

        $threads = $query->fetchAll(static::class);

        $upgraded_threads = array_map(function ($thread) {
            return self::upgradeThread($thread);
        }, $threads);

        $since = 0;
        $olderthan = time();
        foreach ($upgraded_threads as $thread) {
            $active_time = $thread->getLatestActivity(true);
            $since = max($since, $active_time);
            $olderthan = min($olderthan, $active_time);
        }

        $old_count = count($upgraded_threads);

        $upgraded_threads = array_filter($upgraded_threads, function ($thread) use ($user_id) {
            return $thread->isVisibleInStream() && $thread->isReadable($user_id);
        });

        return [$upgraded_threads, $old_count !== count($upgraded_threads), $since, $olderthan];
    }

    /**
     * @param string $institut_id  the ID of an institute
     * @param string $only_in_stream  optional; filter threads by `visible_in_stream`
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     */
    public static function findByInstitut($institut_id, $only_in_stream = false, string $user_id = null)
    {
        return self::findByContext($institut_id, $only_in_stream, self::CTX_TYPE_INST, $user_id);
    }

    /**
     * @param string $seminar_id  the ID of a course
     * @param string $only_in_stream  optional; filter threads by `visible_in_stream`
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     */
    public static function findBySeminar($seminar_id, $only_in_stream = false, string $user_id = null)
    {
        return self::findByContext($seminar_id, $only_in_stream, self::CTX_TYPE_COURSE, $user_id);
    }

    /**
     * @param string $seminar_id  the ID of a course
     * @param string $only_in_stream  optional; filter threads by `visible_in_stream`
     * @param string $context_type  optional; filter threads by `context_type`
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     */
    public static function findByContext(
        $context_id,
        $only_in_stream = false,
        $context_type = self::CTX_TYPE_COURSE,
        string $user_id = null
    ) {
        if (!BlubberThread::findOneBySQL("context_type = :type AND context_id = :context_id AND visible_in_stream = '1' AND content IS NULL AND display_class IS NULL", ['context_id' => $context_id, 'type' => $context_type])) {
            //create the default-thread for this context
            $coursethread = new BlubberThread();
            $coursethread['user_id'] = $user_id ?? $GLOBALS['user']->id;
            $coursethread['external_contact'] = 0;
            $coursethread['context_type'] = $context_type;
            $coursethread['context_id'] = $context_id;
            $coursethread['visible_in_stream'] = 1;
            $coursethread['commentable'] = 1;
            $coursethread->store();
        }
        $query = SQLQuery::table('blubber_threads')
            ->join('blubber_comments', 'blubber_comments', 'blubber_threads.thread_id = blubber_comments.thread_id', 'LEFT JOIN');
        if ($only_in_stream) {
            $query->where("blubber_threads.visible_in_stream = 1");
        }
        $query->where("context", "blubber_threads.context_type = :context_type AND blubber_threads.context_id = :context_id", [
            'context_id' => $context_id,
            'context_type' => $context_type
        ]);
        $query->groupBy('blubber_threads.thread_id');
        $query->orderBy("IFNULL(MAX(blubber_comments.mkdate), blubber_threads.mkdate) DESC");

        $threads = $query->fetchAll(static::class);

        $threads = array_map(function ($thread) {
            return self::upgradeThread($thread);
        }, $threads);
        $threads = array_filter($threads, function ($t) use ($user_id){
            return $t->isVisibleInStream() && $t->isReadable($user_id);
        });
        return $threads;
    }

    /**
     * Export available blubber threads of a given user into a storage object
     * (an instance of the StoredUserData class) for that user.
     *
     * @param StoredUserData $storage object to store data into
     */
    public static function exportUserData(StoredUserData $storage)
    {
        $sorm = self::findBySQL("user_id = ? AND external_contact = '0'", [$storage->user_id]);
        if ($sorm) {
            $field_data = [];
            foreach ($sorm as $row) {
                $field_data[] = $row->toRawArray();
            }
            if ($field_data) {
                $storage->addTabularData(_('Blubber-Threads'), 'blubberthreads', $field_data);
            }
        }
    }

    public function getName()
    {
        if ($this->isOfContextType(self::CTX_TYPE_PUBLIC)) {
            return sprintf(_('Blubber von %s'), $this->user ? $this->user->getFullName() : _('unbekannt'));
        }

        if ($this->isOfContextType(self::CTX_TYPE_PRIVATE)) {
            $names = BlubberParticipation::getParticipantsNamesIn($this->getId(), $GLOBALS['user']->id);

            if (empty($names)) {
                return _('Privatraum');
            }

            $names[] = _('ich');
            $names = implode(', ', $names);
            return mb_substr($names, 0, 60);
        }

        if ($this->isOfContextType(self::CTX_TYPE_COURSE)) {
            if ($this['content']) {
                return mb_substr((string) Course::find($this['context_id'])->name . ': ' . $this['content'], 0, 50) . ' ...';
            } else {
                if ($course = Course::find($this['context_id'])) {
                    return (string) $course->name;
                } else {
                    return _('unbekannt');
                }
            }
        }

        if ($this->isOfContextType(self::CTX_TYPE_INST)) {
            if ($this['content']) {
                return mb_substr((string) Institute::find($this['context_id'])->name . ': ' . $this['content'], 0, 50) . ' ...';
            } else {
                return (string) Institute::find($this['context_id'])->name;
            }
        }

        return _('Ein mysteröser Blubber');
    }

    public function getContentTemplate()
    {
        $template = $GLOBALS['template_factory']->open('blubber/thread_content');
        $template->thread = $this;
        return $template;
    }

    /**
     * Returns a template (or null) to display this in the context container
     */
    public function getContextTemplate()
    {
        if ($this->isOfContextType(self::CTX_TYPE_COURSE)) {
            $teachers       = CourseMember::findBySQL("Seminar_id = ? AND status = 'dozent' ORDER BY position ASC", [$this['context_id']]);
            $tutors         = CourseMember::findBySQL("Seminar_id = ? AND status = 'tutor' ORDER BY position ASC", [$this['context_id']]);
            $students_count = CourseMember::countBySQL("Seminar_id = ? AND status IN ('autor', 'user') ORDER BY position ASC", [$this['context_id']]);

            $template = $GLOBALS['template_factory']->open('blubber/course_context');
            $template->thread         = $this;
            $template->course         = Course::find($this['context_id']);

            $template->teachers       = $teachers;
            $template->tutors         = $tutors;
            $template->students_count = $students_count;
            $template->hashtags       = $this->getHashtags();
            $template->unfollowed     = !$this->isFollowedByUser();
            return $template;
        }

        if ($this->isOfContextType(self::CTX_TYPE_PRIVATE)) {
            $participants = BlubberParticipation::getOrderedParticipantsIn($this->getId());

            $template = $GLOBALS['template_factory']->open('blubber/private_context');
            $template->thread = $this;
            $template->participants = $participants;
            return $template;
        }

        if ($this->isOfContextType(self::CTX_TYPE_PUBLIC)) {
            $template = $GLOBALS['template_factory']->open('blubber/public_context');
            $template->thread = $this;
            return $template;
        }

        if ($this->isOfContextType(self::CTX_TYPE_INST)) {
            $template = $GLOBALS['template_factory']->open('blubber/institute_context');
            $template->thread = $this;
            $template->institute = Institute::find($this['context_id']);
            $template->unfollowed = !$this->isFollowedByUser();
            return $template;
        }
    }

    /**
     * Lets a user follow a thread
     *
     * @param string|null $user_id Id of the user (optional, defaults to current user
     */
    public function addFollowingByUser($user_id = null)
    {
        $query = "DELETE FROM `blubber_threads_followstates`
                  WHERE `thread_id` = :thread_id
                    AND `user_id` = :user_id";
        DBManager::get()->execute($query, [
            ':thread_id' => $this->id,
            ':user_id'   => $user_id ?? $GLOBALS['user']->id,
        ]);
    }

    /**
     * Lets a user unfollow a thread
     *
     * @param string|null $user_id Id of the user (optional, defaults to current user
     */
    public function removeFollowingByUser($user_id = null)
    {
        $query = "REPLACE INTO `blubber_threads_followstates`
                  VALUES (:thread_id, :user_id, 'unfollowed', UNIX_TIMESTAMP())";
        DBManager::get()->execute($query, [
            ':thread_id' => $this->id,
            ':user_id'   => $user_id ?? $GLOBALS['user']->id,
        ]);
    }

    /**
     * Returns whether a user follows a thread.
     *
     * @param string|null $user_id Id of the user (optional, defaults to current user
     * @return bool
     */
    public function isFollowedByUser($user_id = null)
    {
        $query = "SELECT 1
                  FROM `blubber_threads_followstates`
                  WHERE `thread_id` = :thread_id
                    AND `user_id` = :user_id
                    AND `state` = 'unfollowed'";
        $unfollowed = (bool) DBManager::get()->fetchColumn($query, [
            ':thread_id' => $this->id,
            ':user_id'   => $user_id ?? $GLOBALS['user']->id,
        ]);

        return !$unfollowed;
    }

    public function getOpenGraphURLs()
    {
        return OpenGraph::extract($this['content']);
    }

    public function getLatestActivity(bool $include_mkdate = false)
    {
        $newest_comment = BlubberComment::findOneBySQL("thread_id = ? ORDER BY mkdate DESC", [$this->getId()]);
        if ($newest_comment) {
            return $newest_comment->mkdate;
        }
        return $include_mkdate ? $this->mkdate : null;
    }

    public function getURL()
    {
        if ($this->isOfContextType(self::CTX_TYPE_COURSE) || $this->isOfContextType(self::CTX_TYPE_INST)) {
            return URLHelper::getURL('dispatch.php/course/messenger/course/' . $this->getId(), ['cid' => $this['context_id']]);
        }
        return URLHelper::getURL('dispatch.php/blubber/index/' . $this->getId());
    }

    /**
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getLastVisit(string $user_id = null)
    {
        return object_get_visit(
            $this->id,
            $this->getBlubberPluginId(),
            '',
            '',
            $user_id ?? User::findCurrent()->id
        );
    }

    /**
     * Sets the last visit timestamp for this thread
     *
     * @param string|null $user_id
     */
    public function setLastVisit(string $user_id = null): void
    {
        object_set_visit(
            $this->id,
            $this->getBlubberPluginId(),
            $user_id ?? User::findCurrent()->id
        );
    }

    /**
     * Returns the id of the blubber plugin.
     *
     * @return int Id of the plugin
     */
    protected function getBlubberPluginId(): int
    {
        $plugin_info = PluginManager::getInstance()->getPluginInfo(Blubber::class);
        return (int) $plugin_info['id'];

    }

    public function notifyUsersForNewComment($comment)
    {
        $data = $this->getNotificationUsersQueryAndParameters();

        if ($data === false) {
            return;
        }

        $query = "SELECT user_id, `preferred_language` AS language
                  FROM `user_info`
                  WHERE `user_id` IN (
                      {$data['query']}
                  )";

        $statement = DBManager::get()->prepare($query);
        foreach ($data['parameters'] as $key => $value) {
            $statement->bindValue($key, $value);
        }
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);

        $notifications = [];
        foreach ($statement as $row) {
            $user_id  = $row['user_id'];

            // We check if the user has already been notified via @ lookups!
            if (in_array($user_id, $this->already_notified_user_ids)) {
                continue;
            }

            $language = $row['language'] ?? Config::get()->DEFAULT_LANGUAGE;

            if (!isset($notifications[$language])) {
                setTempLanguage(false, $language);

                $user_avatar = Avatar::getAvatar($GLOBALS['user']->id);
                $notification_avatar = Icon::create('blubber')->asImagePath();
                if ($user_avatar->is_customized()) {
                    $notification_avatar = $user_avatar->getURL(Avatar::MEDIUM);
                }

                $notifications[$language] = PersonalNotifications::create([
                    'url'     => $this->getURL(),
                    'text'    => sprintf(_('%s hat eine Nachricht im %s Chat Raum geschrieben.'), get_fullname(), $this->getName()),
                    'avatar'  => $notification_avatar,
                    'dialog'  => true,
                    'html_id' => "blubberthread_{$this->id}",
                ]);

                restoreLanguage();
            }

            $notifications[$language]->link($user_id);
        }

        // We reset the holder back!
        $this->already_notified_user_ids = [];
    }

    /**
     * Returns an array that includes the query and parameters to retrieve the
     * user ids of all users that should be notified by a new post in this
     * thread.
     *
     * The array needs to have the following structure:
     *
     * [
     *     'query' => ...,
     *     'parameters' => ...
     * ]
     *
     * @return array|false
     */
    protected function getNotificationUsersQueryAndParameters()
    {
        // Default set of parameters
        $parameters = [
            ':thread_id' => $this->id,
            ':user_id'   =>  $GLOBALS['user']->id,
        ];

        // Public context: Notify all users that participated
        if ($this->isOfContextType(self::CTX_TYPE_PUBLIC)) {
            $query = "SELECT DISTINCT `user_id`
                      FROM `blubber_comments`
                      WHERE `thread_id` = :thread_id
                          AND `external_contact` = 0
                          AND `user_id` != :user_id";

            if (!$this->external_contact && $this->user_id !== $GLOBALS['user']->id) {
                $query .= " UNION SELECT '{$this->user_id}' AS `user_id`";
            }

            return compact('query', 'parameters');
        }

        // Private context: Notify all participated users
        if ($this->isOfContextType(self::CTX_TYPE_PRIVATE)) {
            $query = "SELECT user_id
                      FROM blubber_participations
                      WHERE thread_id = :thread_id
                        AND external_contact = 0
                        AND user_id != :user_id";

            return compact('query', 'parameters');
        }

        // Course context: Notify all members of the course except the ones that
        // turned the notifications off
        if ($this->isOfContextType(self::CTX_TYPE_COURSE)) {
            $query = "SELECT seminar_user.user_id
                      FROM seminar_user
                      LEFT JOIN blubber_threads_followstates ON (
                          seminar_user.user_id = blubber_threads_followstates.user_id
                          AND blubber_threads_followstates.thread_id = :thread_id
                          AND blubber_threads_followstates.state = 'unfollowed'
                      )
                      WHERE seminar_user.Seminar_id = :context_id
                          AND seminar_user.user_id != :user_id
                          AND blubber_threads_followstates.user_id IS NULL";

            $parameters[':context_id'] = $this->context_id;

            return compact('query', 'parameters');
        }

        // Institute context: Notify all members of the institute
        if ($this->isOfContextType(self::CTX_TYPE_INST)) {
            $query = "SELECT user_inst.user_id
                      FROM user_inst
                      LEFT JOIN blubber_threads_followstates ON (
                          user_inst.user_id = blubber_threads_followstates.user_id
                          AND blubber_threads_followstates.thread_id = :thread_id
                          AND blubber_threads_followstates.state = 'unfollowed'
                      )
                      WHERE Institut_id = :context_id
                          AND user_inst.user_id != :user_id
                          AND blubber_threads_followstates.user_id IS NULL";

            $parameters[':context_id'] = $this->context_id;

            return compact('query', 'parameters');
        }

        return false;
    }

    public function isVisibleInStream()
    {
        return $this['visible_in_stream'];
    }

    /**
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function isWritable(string $user_id = null)
    {
        $user_id = $user_id ?? $GLOBALS['user']->id;
        if ($this->isOfContextType(self::CTX_TYPE_COURSE) || $this->isOfContextType(self::CTX_TYPE_INST)) {
            return $GLOBALS['perm']->have_studip_perm('tutor', $this['context_id'], $user_id);
        } else {
            return $GLOBALS['perm']->have_perm('root', $user_id) || $this['user_id'] === $user_id;
        }
    }

    /**
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function isReadable(string $user_id = null)
    {
        $user_id = $user_id ?? $GLOBALS['user']->id;
        if ($this->isOfContextType(self::CTX_TYPE_PUBLIC)) {
            return true;
        }

        if ($this->isOfContextType(self::CTX_TYPE_PRIVATE)) {
            $is_participated = BlubberParticipation::localUserParticipatesIn($this->getId(), $user_id);
            return $is_participated;
        }

        if ($this->isOfContextType([self::CTX_TYPE_COURSE, self::CTX_TYPE_INST])) {
            return $GLOBALS['perm']->have_studip_perm('user', $this['context_id'], $user_id);
        }

        return false;
    }

    /**
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     */
    public function isCommentable(string $user_id = null)
    {
        return $this->isReadable($user_id) && $this['commentable'];
    }

    public function getAvatar()
    {
        if ($this->isOfContextType(self::CTX_TYPE_COURSE)) {
            return CourseAvatar::getAvatar($this['context_id'])->getURL(Avatar::MEDIUM);
        }

        if ($this->isOfContextType(self::CTX_TYPE_INST)) {
            return InstituteAvatar::getAvatar($this['context_id'])->getURL(Avatar::MEDIUM);
        }

        if ($this->isOfContextType(self::CTX_TYPE_PRIVATE)) {
            $participants = BlubberParticipation::getParticipantsIn($this->getId());

            if (count($participants) === 1) {
                return Avatar::getAvatar($participants[0]['user_id'])->getURL(Avatar::MEDIUM);
            }

            if (count($participants) === 2 && $participants[0]['user_id'] === $GLOBALS['user']->id && !$participants[0]['external_contact']) {
                return Avatar::getAvatar($participants[1]['user_id'])->getURL(Avatar::MEDIUM);
            }

            if (count($participants) === 2 && $participants[1]['user_id'] === $GLOBALS['user']->id && !$participants[1]['external_contact']) {
                return Avatar::getAvatar($participants[0]['user_id'])->getURL(Avatar::MEDIUM);
            }

            return Icon::create('group3')->asImagePath();
        }

        if ($this->isOfContextType(self::CTX_TYPE_PUBLIC)) {
            return Icon::create('globe')->asImagePath();
        }

        return CourseAvatar::getNobody()->getURL(Avatar::MEDIUM);
    }

    public function getJSONData($limit_comments = 50, $user_id = null, $search = null)
    {
        $user_id || $user_id = $GLOBALS['user']->id;
        $output = [
            'thread_posting'  => $this->toRawArray(),
            'context_info'    => '',
            'comments'        => [],
            'more_up'         => 0,
            'more_down'       => 0,
            'unseen_comments' => BlubberComment::countBySQL("thread_id = ? AND mkdate >= ? AND user_id != ?", [
                $this->getId(),
                $this->getLastVisit(),
                $user_id
            ]),
            'notifications' => $this->mayDisableNotifications(),
            'followed' => $this->isFollowedByUser(),
        ];
        $context_info = $this->getContextTemplate();
        if ($context_info) {
            $output['context_info'] = $context_info->render();
        }
        $output['thread_posting']['name'] = $this->getName();
        $output['thread_posting']['user_name'] = $this->user ? $this->user->getFullName() : _("unbekannt");
        $output['thread_posting']['user_username'] = $this->user ? $this->user['username'] : "";
        $output['thread_posting']['avatar'] = Avatar::getAvatar($this['user_id'])->getURL(Avatar::MEDIUM);
        $output['thread_posting']['html'] = $this->getContentTemplate()->render();
        $output['thread_posting']['writable'] = $this->isWritable() ? 1 : 0;
        $output['thread_posting']['chdate'] = (int) $output['thread_posting']['chdate'];
        $output['thread_posting']['mkdate'] = (int) $output['thread_posting']['mkdate'];

        if ($search) {
            $query = "SELECT blubber_comments.*
                      FROM blubber_comments
                      WHERE blubber_comments.thread_id = :thread_id
                          AND content LIKE :search
                      ORDER BY mkdate DESC";
            $statement = DBManager::get()->prepare($query);
            $statement->execute([
                'thread_id' => $this->getId(),
                'search'    => '%' . $search . '%'
            ]);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $query = "SELECT blubber_comments.*
                      FROM blubber_comments
                      WHERE blubber_comments.thread_id = :thread_id
                      ORDER BY mkdate DESC
                      LIMIT :limit";
            $statement = DBManager::get()->prepare($query);
            $statement->execute([
                'thread_id' => $this->getId(),
                'limit' => $limit_comments + 1,
            ]);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > $limit_comments) {
                $output['more_up'] = 1;
            }
        }

        foreach ($result as $data) {
            $comment = BlubberComment::buildExisting($data);
            $output['comments'][] = $comment->getJSONData();
        }

        return $output;
    }

    /**
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function markAsRead(string $user_id = null)
    {
        $user_id = $user_id ?? $GLOBALS['user']->id;

        $statement = DBManager::get()->prepare("
            UPDATE personal_notifications_user
                INNER JOIN personal_notifications USING (personal_notification_id)
            SET personal_notifications_user.seen = '1'
            WHERE personal_notifications_user.user_id = :user_id
                AND personal_notifications.html_id = :html_id
        ");
        $statement->execute([
            'user_id' => $user_id,
            'html_id' => "blubberthread_".$this->getId()
        ]);

        $this->setLastVisit($user_id);
    }

    public function getHashtags($since = null)
    {
        $query = "
            SELECT *
            FROM blubber_comments
            WHERE thread_id = ".DBManager::get()->quote($this->getId())."
                AND content REGEXP '(^|[[:blank:]]|[[:cntrl:]])#[[:graph:]]' > 0
        ";
        if ($since) {
            $get_hashtags = DBManager::get()->query($query ."
                    AND mkdate > ".DBManager::get()->quote($since)."
            ");
        } else {
            $get_hashtags = DBManager::get()->query($query);
        }
        $hashtags = [];
        foreach ($get_hashtags->fetchAll(PDO::FETCH_ASSOC) as $comment_data) {
            $matched = preg_match_all(
                '/'. BlubberFormat::REGEXP_HASHTAG . '/uS',
                $comment_data['content'],
                $matches
            );

            if ($matched === 0) {
                continue;
            }

            foreach ($matches[1] as $tag) {
                if (!isset($hashtags[mb_strtolower($tag)])) {
                    $hashtags[mb_strtolower($tag)] = 0;
                }
                $hashtags[mb_strtolower($tag)] += 1;
            }
        }
        asort($hashtags);
        return array_reverse($hashtags);
    }

    /**
     * Returns all Seminar_ids to courses I am member of and in which blubber
     * is an active plugin.
     *
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     * @return array of string : array of Seminar_ids
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getMyBlubberCourses(string $user_id = null)
    {
        $user_id = $user_id ?? $GLOBALS['user']->id;
        if ($GLOBALS['perm']->have_perm('admin', $user_id)) {
            return [];
        }

        $is_deputy = Config::get()->DEPUTIES_ENABLE && Deputy::countByUser_id($user_id) > 0;
        $blubber_plugin_info = PluginManager::getInstance()->getPluginInfo('Blubber');

        $parameters = [
            'me'                => $user_id,
            'blubber_plugin_id' => $blubber_plugin_info['id'],
        ];

        $query = "SELECT seminar_user.Seminar_id
                  FROM seminar_user
                  INNER JOIN tools_activated
                    ON plugin_id = :blubber_plugin_id
                       AND tools_activated.range_id = seminar_user.Seminar_id
                  WHERE seminar_user.user_id = :me";

        $my_courses = DBManager::get()->fetchFirst($query, $parameters);

        if ($is_deputy) {
            $query = "SELECT deputies.range_id
                      FROM deputies
                      INNER JOIN tools_activated
                    ON plugin_id = :blubber_plugin_id
                       AND tools_activated.range_id = deputies.range_id
                  WHERE deputies.user_id = :me";
            $my_courses = array_merge(
                $my_courses,
                DBManager::get()->fetchFirst($query, $parameters)
            );
        }
        return $my_courses;
    }

    /**
     * @param ?string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getMyBlubberInstitutes(string $user_id = null)
    {
        $user_id = $user_id ?? $GLOBALS['user']->id;
        if ($GLOBALS['perm']->have_perm('root', $user_id)) {
            return [];
        }

        $query = "SELECT Institut_id
                  FROM user_inst
                  WHERE user_id = ?";
        $institut_ids = DBManager::get()->fetchFirst($query, [$user_id]);
        $blubberplugin = PluginManager::getInstance()->getPlugin(Blubber::class);
        if (!$blubberplugin) {
            return [];
        }

        foreach ($institut_ids as $index => $institut_id) {
            if (!PluginManager::getInstance()->isPluginActivated($blubberplugin->getPluginId(), $institut_id)) {
                unset($institut_ids[$index]);
            }
        }
        return $institut_ids;
    }

    /**
     * Returns whether the notifications for this thread may be disabled.
     *
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     * @return bool
     */
    public function mayDisableNotifications(string $user_id = null): bool
    {
        // Notifications may always be disabled for global blubber stream
        if ($this->id === 'global') {
            return true;
        }

        // Notifications may not be disabled outside of course and institute
        // streams
        if (!$this->isOfContextType([self::CTX_TYPE_COURSE, self::CTX_TYPE_INST])) {
            return false;
        }

        // Only users with permission below admin may disable the notifications.
        $user_id = $user_id ?? $GLOBALS['user']->id;

        return !$GLOBALS['perm']->have_perm('admin', $user_id);
    }

    /**
     * Count all unseen comments of this thread.
     *
     * @param string $user_id  optional; use this ID instead of $GLOBALS['user']->id
     *
     */
    public function countUnseenComments(string $user_id = null): int
    {
        $user_id = $user_id ?? User::findCurrent();
        return \BlubberComment::countBySQL(
            'thread_id = ? AND mkdate >= ? AND user_id != ?',
            [
                $this->getId(),
                $this->getLastVisit($user_id) ?: object_get_visit_threshold(),
                $user_id
            ]
        );
    }

    public static function findPrivateThreadBetween(string $first_party, string $second_party)
    {
        return self::findOneBySQL(
                "JOIN blubber_participations
                    ON blubber_participations.thread_id = blubber_threads.thread_id
                JOIN blubber_participations AS blubber_participations_me
                    ON blubber_participations_me.thread_id = blubber_threads.thread_id
                JOIN blubber_participations AS blubber_participations_friend
                    ON blubber_participations_friend.thread_id = blubber_threads.thread_id
                WHERE blubber_threads.context_type = 'private'
                    AND blubber_threads.parent_id IS NULL
                    AND blubber_participations_me.user_id = :first_party
                    AND blubber_participations_friend.user_id = :second_party
                GROUP BY blubber_threads.thread_id
                HAVING COUNT(blubber_participations.user_id) = 2
                ORDER BY blubber_threads.mkdate DESC
                LIMIT 1",
                [
                    'first_party' => $first_party,
                    'second_party' => $second_party,
                ]
            );
    }

    public function createSubThread(array $force_fields = []): ?self
    {
        $data = $this->toArray([
            'context_type',
            'context_id',
            'external_contact',
            'display_class',
            'visible_in_stream',
            'commentable',
            'metadata',
        ]);

        $user_id = $GLOBALS['user']->id;
        if (!empty($force_fields['user_id'])) {
            $user_id = $force_fields['user_id'];
        }

        $data['user_id'] = $user_id;

        $content = '';
        if (!empty($force_fields['content'])) {
            $content = $force_fields['content'];
        }
        $data['content'] = $content;

        $data['parent_id'] = $this->getId();

        $sub_thread = self::create($data);

        BlubberParticipation::transferParticipantsToSubThread($sub_thread->parent_id, $sub_thread->id);

        return $sub_thread ?? null;
    }

    public static function findByParent_id(string $parent_id): array
    {
        $threads = self::findBySQL(
            "parent_id = :parent_id ORDER BY mkdate ASC",
            ['parent_id' => $parent_id]
        );

        return !empty($threads) ? array_map(function ($thread) {
            return self::upgradeThread($thread);
        }, $threads) : [];
    }
}
