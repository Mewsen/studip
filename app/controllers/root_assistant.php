<?php

class RootAssistantController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!$GLOBALS['perm']->have_perm('root')) {
            throw new AccessDeniedException();
        }
        unset($_SESSION['messages']['release-notes']);
        PageLayout::setTitle(sprintf(_('Änderungen seit der Stud.IP-Version %s'), Config::get()->MIGRATION_START_VERSION));
    }

    public function index_action()
    {
        $this->release_notes = $this->fetchReleaseNotes();
        $this->configurations = $this->fetchNewConfiguration();
    }

    public function seen_action()
    {
        if (!Request::isPost()) {
            throw new MethodNotAllowedException();
        }

        if (Request::bool('seen')) {
            Config::get()->store('UPDATE_NEWS_SEEN', true);
        }

        $this->response->add_header('X-Dialog-Close', 1);
        $this->render_nothing();
    }

    private function getCurrentVersion(): int
    {
        return (int)DBManager::get()->fetchColumn(
            "SELECT SUBSTRING(REPLACE(MAX(branch), '.', ''), 1, 2) FROM schema_version WHERE domain = 'studip'"
        );
    }

    private function getTargetVersion(): int
    {
        return str_replace('.', '', StudipVersion::getStudipVersion());
    }

    private function fetchReleaseNotes(): array
    {
        $current_version = $this->getCurrentVersion();
        $target_version  = $this->getTargetVersion();
        $releases        = glob($GLOBALS['STUDIP_BASE_PATH'] . '/doc/release-notes/*.md');
        $releases        = (array)array_filter($releases, function ($release) use ($current_version, $target_version) {
            $version = (int)basename($release, '.md');
            return $version >= $current_version && $version <= $target_version;
        });

        $releases[] = $GLOBALS['STUDIP_BASE_PATH'] . '/RELEASE-NOTES.md';

        $parser = new Parsedown();

        $release_notes = [];

        foreach ($releases as $release) {
            $parser->setSafeMode(true);
            $content = @file_get_contents($release);

            if ($content) {
                $content = $parser->text($content);

                $headline_pattern = '/<h1>(.*?)<\/h1>/';

                preg_match($headline_pattern, $content, $headline_matches);

                $content = preg_replace($headline_pattern, '', $content);

                $release_note = [
                    'headline' => $headline_matches[1] ?? '',
                    'date'     => $date_matches[1] ?? '',
                    'content'  => $content,
                ];

                $release_notes[] = $release_note;
            }
        }


        return $release_notes;
    }


    /**
     * @return array
     */
    private function fetchNewConfiguration(): array
    {
        if (!Config::get()->MIGRATION_START_VERSION && !Config::get()->MIGRATION_START_TIME) {
            return [];
        }
        return ConfigEntry::findBySQL('mkdate >= ?', [Config::get()->MIGRATION_START_TIME]);
    }

}
