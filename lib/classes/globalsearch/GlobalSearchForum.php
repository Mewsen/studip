<?php
/**
 * GlobalSearchModule for forum entries
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       4.1
 */
class GlobalSearchForum extends GlobalSearchModule implements GlobalSearchFulltext
{
    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return _('Forenbeiträge');
    }

    /**
     * @inheritDoc
     */
    public static function getFilters(): array
    {
        return ['semester'];
    }

    /**
     * @inheritDoc
     */
    public static function getSQL($search, $filter, $limit): string
    {
        $search = str_replace(" ", "% ", $search);
        $query = DBManager::get()->quote("%$search%");

        // visibility
        $seminaruser = '';
        if (!$GLOBALS['perm']->have_perm('admin')) {
            $seminaruser = " AND EXISTS (
                SELECT 1 FROM `seminar_user`
                WHERE `forum_postings`.`range_id` = `seminar_user`.`seminar_id`
                  AND `seminar_user`.`user_id` = " . DBManager::get()->quote($GLOBALS['user']->id) . "
              ) ";
        }

        // generate SQL condition for the semester filter in the sidebar
        $semester_condition = '';
        if (
            !empty($filter['category'])
            && in_array($filter['category'], [self::class, 'show_all_categories'])
        ) {
            if (!empty($filter['semester'])) {
                if ($filter['semester'] === 'future') {
                    $semester = Semester::findCurrent();
                    $next_semester = Semester::findNext();
                    $semester_end = $next_semester ? $next_semester->ende : $semester->ende;
                } else {
                    $semester = Semester::findByTimestamp($filter['semester']);
                    $semester_end = $semester->ende;
                }
                $semester_condition = " AND (`mkdate` >= " . DBManager::get()->quote($semester['beginn']) .
                            " AND `mkdate` <= " . DBManager::get()->quote($semester_end) . ") ";
            }
        }

        // anonymous postings
        if (!$GLOBALS['perm']->have_perm('root') && Config::get()->FORUM_ANONYMOUS_POSTINGS) {
            $anonymous = "`anonymous` = 0 AND";
        } else {
            $anonymous = "";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS `forum_postings`.*
                FROM `forum_postings`
                WHERE {$anonymous} (
                    `content` LIKE {$query}
                )
                {$semester_condition}
                {$seminaruser}
                ORDER BY `chdate` DESC
                LIMIT " . $limit;

        return $sql;
    }

    /**
     * @inheritDoc
     */
    public static function filter($data, $search): array
    {
        $user = self::fromCache("user/{$data['user_id']}", function () use ($data) {
            return User::findFull($data['user_id']);
        });
        $range = self::fromCache("range/{$data['range_id']}", function () use ($data) {
            return get_object_by_range_id($data['range_id']);
        });

        // Get posts author name
        if (!$user) {
            $author_name = _('Unbekannt');
        } else if ($user->id !== User::findCurrent()->id && $data['anonymous']) {
            $author_name = _('Anonym');
        } else {
            $author_name = self::mark($user->getFullName(), $search);
        }

        // Get additional info
        $additional = sprintf(
            _('Beitrag von %1$s in %2$s'),
            $author_name,
            $range->getFullName()
        );

        // Clear content from blockquotes
        $filtered_content = self::blockquote_filter($data['content']);

        $range_avatar = match (get_class($range)) {
            Institute::class => InstituteAvatar::getAvatar($range->id)->getURL(Avatar::NORMAL),
            Course::class => CourseAvatar::getAvatar($range->id)->getURL(Avatar::NORMAL)
        };

        //in case the search query is found in either $author_name or $filtered_content (via direct_search), the result should be returned
        if (mb_strpos($author_name, "<mark>") !== false || self::direct_search($filtered_content, $search)) {
            $result = [
                'id'          => $data['posting_id'],
                'name'        => $author_name,
                'url'         => URLHelper::getURL(
                    "dispatch.php/course/forum/discussions/show/{$data['discussion_id']}#post_{$data['posting_id']}",
                    ['cid' => $range->id, 'q' => $search],
                    true
                ),
                'img'         => $range_avatar,
                'date'        => strftime('%x', $data['chdate']),
                'description' => self::mark($filtered_content, $search, true),
                'additional'  => htmlReady($additional),
                'expand' => URLHelper::getURL('dispatch.php/course/forum/search', [
                    'cid'   => $range->id,
                    'q'     => $search
                ]),
                'expandtext'  => _('Im Forum dieser Veranstaltung suchen'),
            ];

            return $result;
        }

        return [];
    }

    /**
     * Looks for blockquote and removes it from content in recursive mode
     *
     * @param string $content data content
     * @return string purified content (without blockquote)
     */
    public static function blockquote_filter($content): string
    {
        $beg = '<blockquote>';
        $end = '</blockquote>';
        $beg_pos = mb_strpos($content, $beg);
        $end_pos = mb_strpos($content, $end);
        if ($beg_pos === false || $end_pos === false) {
            return $content;
        }

        $quote = mb_substr($content, $beg_pos, ($end_pos + mb_strlen($end)) - $beg_pos);
        $most_inner_element = mb_substr($quote, mb_strrpos($quote, $beg), (mb_strpos($quote, $end) + mb_strlen($end)) - mb_strrpos($quote, $beg));

        //recursive call!
        return self::blockquote_filter(str_replace($most_inner_element, '', $content));
    }

    /**
     * Search the query directly inside the string
     *
     * @param string $string text to be searched
     * @param string $query query to be found
     *
     * @return boolean $found in case of finding true, otherwise false
     */
    public static function direct_search($string, $query)
    {
        $found = false;
        $string = strip_tags($string);
        $query = trim($query);
        $quoted = preg_quote($query, '/');
        if (preg_match("/{$quoted}/Si", $string)) {
            $found = true;
        }
        return $found;
    }

    /**
     * @inheritDoc
     */
    public static function enable(): void
    {
        DBManager::get()->exec("ALTER TABLE `forum_postings` ADD FULLTEXT INDEX globalsearch (`content`)");
    }

    /**
     * @inheritDoc
     */
    public static function disable(): void
    {
        DBManager::get()->exec("DROP INDEX globalsearch ON `forum_postings`");
    }

    /**
     * @inheritDoc
     */
    public static function getSearchURL($searchterm): string
    {
        return URLHelper::getURL('dispatch.php/search/globalsearch', [
            'q'        => $searchterm,
            'category' => self::class
        ]);
    }
}
