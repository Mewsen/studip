<?php

use \Studip\Activity\ActivityProvider;

class ActivityfeedController extends AuthenticatedController
{
    public function save_action()
    {
        if (Config::get()->ACTIVITY_FEED === NULL) {
            Config::get()->create('ACTIVITY_FEED', [
                'range' => 'user',
                'type' => 'array',
                'description' => 'Einstellungen des Activity-Widgets']
            );
        }

        $provider = Request::getArray('provider');

        UserConfig::get($GLOBALS['user']->id)->store('ACTIVITY_FEED', $provider);

        $this->response->add_header('X-Dialog-Close', 1);
        $this->response->add_header('X-Dialog-Execute', 'STUDIP.ActivityFeed.updateFilter');

        $this->render_json($provider);
    }

    /**
     * return a list for all providers for every context
     *
     * @return array
     */
    private function getAllModules()
    {
        $modules = [];

        $modules['system'] = [
            'news'         => _('Ankündigungen'),
            'blubber'      => _('Blubber')
        ];

        $modules[Context::COURSE] = [
            'forum'        => _('Forum'),
            'participants' => _('Teilnehmende'),
            'documents'    => _('Dateien'),
            'wiki'         => _('Wiki'),
            'schedule'     => _('Ablaufplan'),
            'news'         => _('Ankündigungen'),
            'blubber'      => _('Blubber'),
            'courseware'   => _('Courseware')
        ];

        $modules[Context::INSTITUTE] = $modules[Context::COURSE];
        unset($modules[Context::INSTITUTE]['participants']);
        unset($modules[Context::INSTITUTE]['schedule']);

        $standard_plugins = PluginManager::getInstance()->getPlugins(StandardPlugin::class);
        foreach ($standard_plugins as $plugin) {
            if ($plugin instanceof ActivityProvider) {
                $modules[Context::COURSE][$plugin->getPluginName()] = $plugin->getPluginName();
                $modules[Context::INSTITUTE][$plugin->getPluginName()] = $plugin->getPluginName();
            }
        }

        $modules[Context::USER] = [
            'message'      => _('Nachrichten'),
            'news'         => _('Ankündigungen'),
            'blubber'      => _('Blubber'),
        ];

        $homepage_plugins = PluginEngine::getPlugins(HomepagePlugin::class);
        foreach ($homepage_plugins as $plugin) {
            if ($plugin->isActivated($GLOBALS['user']->id, 'user')) {
                if ($plugin instanceof ActivityProvider) {
                    $modules[Context::USER][] = $plugin;
                }
            }
        }

        return $modules;
    }

    public function configuration_action()
    {
        $this->config = UserConfig::get($GLOBALS['user']->id)->getValue('ACTIVITY_FEED');
        $this->modules = $this->getAllModules();
        $this->context_translations = [
            Context::COURSE    => _('Veranstaltungen'),
            Context::INSTITUTE => _('Einrichtungen'),
            Context::USER      => _('Persönlich'),
            'system'            => _('Global')
        ];

        PageLayout::setTitle(_('Aktivitäten konfigurieren'));
    }

    public function load_action(): void
    {
        $user = User::findCurrent();

        // failsafe einbauen - falls es keine älteren Aktivitäten mehr im System gibt, Abbruch!

        $oldest_activity = \Studip\Activity\Activity::getOldestActivity();
        $max_age = $oldest_activity ? $oldest_activity->mkdate : time();


        $contexts = [];

        // create system context
        $system_context = new \Studip\Activity\SystemContext($user);
        $contexts[] = $system_context;

        $contexts[] = new \Studip\Activity\UserContext($user, $user);
        $user->contacts->each(function ($another_user) use (&$contexts, $user) {
            $contexts[] = new \Studip\Activity\UserContext($another_user, $user);
        });

        if (!in_array($user->perms, ['admin','root'])) {
            // create courses and institutes context
            foreach (\Course::findMany($user->course_memberships->pluck('seminar_id')) as $course) {
                $contexts[] = new \Studip\Activity\CourseContext($course, $user);
            }
            foreach (\Institute::findMany($user->institute_memberships->pluck('institut_id')) as $institute) {
                $contexts[] = new \Studip\Activity\InstituteContext($institute, $user);
            }
        }


        // add filters
        $filter = new \Studip\Activity\Filter();

        $start = Request::int('start', strtotime('yesterday'));
        $end   = Request::int('end',   time());


        $scrollfrom = Request::int('scrollfrom', false);
        $filtertype = Request::get('filtertype', '');

        $objectType = Request::get('object_type');
        $filter->setObjectType($objectType);

        $objectId = Request::get('object_id');
        $filter->setObjectId($objectId);

        $context = Request::get('context_type');
        $filter->setContext($context);

        $contextId = Request::get('context_id');
        $filter->setContextId($contextId);

        if (!empty($filtertype)) {
            $filter->setType(json_decode($filtertype));
        }

        if ($scrollfrom) {
            // shorten "watch-window" by one second to prevent duplication of activities
            $scrollfrom -= 1;

            if ($scrollfrom > $max_age){
                $end = $scrollfrom;
                $start = strtotime('yesterday', $end);
                $data = [];

                $backtrack = 1;

                while (empty($data)) {
                    $filter->setStartDate($start);
                    $filter->setEndDate($end);

                    $data = $this->getStreamData($contexts, $filter);

                    if ($start < $max_age) {
                        break;
                    }

                    // move "watch-window" back one day at a time
                    $end = $start - 1;
                    $start = strtotime("-{$backtrack} days", $start);

                    // enforce maximum "watch-window", currently 2 weeks
                    $backtrack = min(14, $backtrack + 1);
                }
            } else {
                $data = false;
            }
        } else {
            $filter->setStartDate($start);
            $filter->setEndDate($end);
            $data = $this->getStreamData($contexts, $filter);
        }

        // set etag for preventing resending the same stuff over and over again
        $etag = md5(serialize($data));
        $this->response->add_header('ETag', '"' . $etag . '"');
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $this->etagMatches($etag, $_SERVER['HTTP_IF_NONE_MATCH'])) {
            $this->set_status(304);
            $this->render_nothing();
            return;
        }
        if (isset($_SERVER['HTTP_IF_MATCH']) && !$this->etagMatches($etag, $_SERVER['HTTP_IF_MATCH'])) {
            $this->set_status(412);
            $this->render_nothing();
            return;
        }

        $this->render_json($data);
    }

    /**
     *  private helper function to get stream data for given contexts and filter
     *
     * @param $contexts
     * @param $filter
     * @return array
     */

    private function getStreamData($contexts, $filter): array
    {
        $stream = new Studip\Activity\Stream($contexts, $filter);
        $data = $stream->toArray();

        foreach ($data as $key => $act) {
            $actor = [
                'type' => $act['actor_type'],
                'id'   => $act['actor_id'],
            ];

            if ($act['actor_type'] == 'user') {
                $a_user = \User::findFull($act['actor_id']);
                $actor['details'] = $this->getMiniUser($a_user ?: new \User());
            } elseif ($act['actor_type'] === 'anonymous') {
                $actor['details'] = [
                    'name' => _('Anonym'),
                ];
            }

            unset($data[$key]['actor_type']);
            unset($data[$key]['actor_id']);

            $data[$key]['actor'] = $actor;
        }

        return $data;
    }

    private function getMiniUser(User $user): array
    {
        $avatar = \Avatar::getAvatar($user->id);

        return [
            'id'              => $user->id,
            'name'            => $this->getNamesOfUser($user),
            'avatar_small'    => $avatar->getURL(\Avatar::SMALL),
            'avatar_medium'   => $avatar->getURL(\Avatar::MEDIUM),
            'avatar_normal'   => $avatar->getURL(\Avatar::NORMAL),
            'avatar_original' => $avatar->getURL(\Avatar::NORMAL)
        ];
    }

    private function getNamesOfUser(User $user): array
    {
        return [
            'username'  => $user->username,
            'formatted' => $user->getFullName(),
            'family'    => $user->nachname,
            'given'     => $user->vorname,
            'prefix'    => $user->title_front,
            'suffix'    => $user->title_rear,
        ];
    }

    // Helper method checking if a ETag value list includes the current ETag.
    private function etagMatches(string $etag, string $list)
    {
        if ($list === '*') {
            return true;
        }

        return in_array(
            $etag,
            preg_split('/\s*,\s*/', $list)
        );
    }

}
