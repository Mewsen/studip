<?php
require_once 'lib/models/MassMail/MassMailPermission.php';
require_once 'lib/cronjobs/send_massmails.php';

return new class extends Migration
{
    use DatabaseMigrationTrait;

    public function description()
    {
        return 'Integrate the functionality from the Garuda plugin into the Stud.IP core.';
    }

    protected function up()
    {
        // Messages and templates
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_messages` (
            `message_id` INT NOT NULL AUTO_INCREMENT,
            `sender_id` CHAR(32) COLLATE latin1_bin,
            `author_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `send_at_date` INT,
            `target` ENUM('all', 'students', 'employees', 'lecturers', 'courses', 'usernames') COLLATE latin1_bin,
            `config` LONGTEXT,
            `exclude_users` LONGTEXT,
            `cc` TEXT,
            `subject` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `folder_id` CHAR(32) COLLATE latin1_bin,
            `is_template` TINYINT(1) NOT NULL DEFAULT 0,
            `locked` TINYINT(1) NOT NULL DEFAULT 0,
            `sent` TINYINT(1) NOT NULL DEFAULT 0,
            `protected` TINYINT(1) NOT NULL DEFAULT 0,
            `mkdate` INT UNSIGNED NOT NULL,
            `chdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`message_id`),
            INDEX author_id (`author_id`)
        )");

        // Permissions for using this functionality
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_permissions` (
            `permission_id` INT NOT NULL AUTO_INCREMENT,
            `institute_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `min_perm` ENUM ('admin', 'dozent', 'tutor', 'autor') COLLATE latin1_bin NOT NULL DEFAULT 'admin',
            `mkdate` INT UNSIGNED NOT NULL,
            `chdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`permission_id`),
            UNIQUE INDEX institute_id (`institute_id`)
        )");

        // Allowed degrees
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_permission_degree` (
            `permission_id` INT NOT NULL,
            `degree_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `mkdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`permission_id`, `degree_id`),
            INDEX degree_id (`degree_id`)
        )");

        // Allowed subjects of study
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_permission_subject` (
            `permission_id` INT NOT NULL,
            `subject_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `mkdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`permission_id`, `subject_id`),
            INDEX subject_id (`subject_id`)
        )");

        // Allowed institutes
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_permission_institute` (
            `permission_id` INT NOT NULL,
            `institute_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `mkdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`permission_id`, `institute_id`),
            INDEX institute_id (`institute_id`)
        )");

        // User filters
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_filter` (
            `message_id` INT NOT NULL,
            `filter_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `mkdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`message_id`, `filter_id`),
            INDEX filter_id (`filter_id`)
        )");

        // User-specific tokens
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_tokens` (
            `token_id` INT NOT NULL AUTO_INCREMENT,
            `message_id` INT NOT NULL,
            `user_id` CHAR(32) COLLATE latin1_bin,
            `token` VARCHAR(1024) NOT NULL,
            `mkdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`token_id`),
            INDEX message_id (`message_id`)
        )");

        // Serial mail markers
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `massmail_markers` (
            `marker_id` INT NOT NULL AUTO_INCREMENT,
            `marker` VARCHAR(255) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `type` ENUM('text', 'database', 'function', 'token') COLLATE latin1_bin,
            `description` TEXT,
            `root_only` TINYINT(1) UNSIGNED DEFAULT 0,
            `replacement` TEXT,
            `replacement_female` TEXT,
            `replacement_unknown` TEXT,
            `position` TINYINT(1) UNSIGNED,
            `mkdate` INT UNSIGNED NOT NULL,
            `chdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`marker_id`)
        )");

        $markers = [
            [
                'marker' => 'FULLNAME',
                'name' => 'Voller Name',
                'type' => 'database',
                'description' => _('Hier wird der volle Name der jeweiligen Person eingesetzt, z.B. "Prof. Max Mustermann, PhD".'),
                'replacement' => 'user_info.title_front {{FIRSTNAME}} {{LASTNAME}} user_info.title_rear',
                'position' => 2
            ],
            [
                'marker' => 'FIRSTNAME',
                'name' => 'Vorname',
                'type' => 'database',
                'description' => _('Hier wird der Vorname der jeweiligen Person eingesetzt.'),
                'replacement' => 'auth_user_md5.Vorname',
                'position' => 3
            ],
            [
                'marker' => 'LASTNAME',
                'name' => 'Nachname',
                'type' => 'database',
                'description' => _('Hier wird der Nachname der jeweiligen Person eingesetzt.'),
                'replacement' => 'auth_user_md5.Nachname',
                'position' => 4
            ],
            [
                'marker' => 'USERNAME',
                'name' => 'Benutzername',
                'type' => 'database',
                'description' => _('Hier wird der Benutzername der jeweiligen Person eingesetzt.'),
                'replacement' => 'auth_user_md5.username',
                'position' => 5
            ],
            [
                'marker' => 'SEHRGEEHRTE',
                'name' => 'Anrede mit vollem Namen',
                'type' => 'text',
                'description' => _('Hier wird eine Anrede erzeugt: "Sehr geehrte Michaela Musterfrau" bzw. "Sehr geehrter Max Mustermann".'),
                'replacement' => 'Sehr geehrter {{FULLNAME}}',
                'replacement_female' => 'Sehr geehrte {{FULLNAME}}',
                'replacement_unknown' => 'Sehr geehrte/r {{FULLNAME}}',
                'position' => 1
            ],
            [
                'marker' => 'DEARSIRMADAM',
                'name' => 'Anrede (englisch) mit vollem Namen',
                'type' => 'text',
                'description' => _('Creates a Salutation: "Dear Jane Doe" or "Dear John Doe".'),
                'replacement' => 'Dear {{FULLNAME}}',
                'position' => 6
            ],
            [
                'marker' => 'TOKEN',
                'name' => 'Personalisierter Code o.ä.',
                'type' => 'token',
                'description' => _('Hier wird ein persönlicher Teilnahmecode o.ä. aus einer hochgeladenen Datei eingesetzt.'),
                'replacement' => 'massmail_tokens.token',
                'root_only' => 1,
                'position' => 7
            ]
        ];

        foreach ($markers as $data) {
            \MassMail\MassMailMarker::create($data);
        }

        if (empty(RolePersistence::getRoleIdByName(\MassMail\MassMailPermission::MASSMAIL_ROOT_ROLE))) {
            RolePersistence::saveRole(
                new Role(Role::UNKNOWN_ROLE_ID, \MassMail\MassMailPermission::MASSMAIL_ROOT_ROLE)
            );
        }

        DBManager::get()->exec("INSERT IGNORE INTO `config`
             (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
             VALUES
             (
              'MASSMAIL_LECTURER_SEM_CATEGORIES',
              '[1]',
              'array',
              'global',
              'MassMail',
              UNIX_TIMESTAMP(),
              UNIX_TIMESTAMP(),
              'Veranstaltungskategorien, die für die Ermittlung aktiver Lehrender berücksichtigt werden'
             )"
        );
        DBManager::get()->exec("INSERT IGNORE INTO `config`
             (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
             VALUES
             (
              'MASSMAIL_GC_DAYS',
              '7',
              'integer',
              'global',
              'MassMail',
              UNIX_TIMESTAMP(),
              UNIX_TIMESTAMP(),
              'Anzahl Tage, nach denen bereits verschickte Nachrichten aus der Datenbank entfernt werden (0 bedeutet nie)'
             )"
        );

        SendMassmailsJob::register()->schedulePeriodic(-15)->activate();

        /*
         * Extend userfilter table so that we know from which context a specific UserFilter comes from,
         * allowing us to check permissions for editing.
         */
        if (!$this->columnExists('userfilter', 'range_id') && !$this->columnExists('userfilter', 'range_type')) {
            DBManager::get()->exec("ALTER TABLE `userfilter`
                ADD `range_id` VARCHAR(32) COLLATE `latin1_bin` NOT NULL AFTER `filter_id`,
                ADD `range_type` VARCHAR(255) COLLATE `latin1_bin` NOT NULL AFTER `range_id`");
        }

        /*
         * Set context values for existing userfilters (we only need to consider filters from admission rules
         * as only those are part of the core so far)
         */

        // First: filters from ConditionalAdmissions
        $conditions = DBManager::get()->fetchAll(
            "SELECT DISTINCT c.`filter_id`, r.`set_id` FROM `admission_condition` c
             JOIN `courseset_rule` r USING (`rule_id`)"
        );
        // Second: filters from PreferentialAdmissions
        $preferential = DBManager::get()->fetchAll(
            "SELECT DISTINCT p.`condition_id` AS filter_id, r.`set_id` FROM `prefadmission_condition` p
             JOIN `courseset_rule` r USING (`rule_id`)"
        );
        foreach (array_merge($conditions, $preferential) as $filter) {
            DBManager::get()->execute(
                "UPDATE `userfilter` SET `range_id` = :range, `range_type` = :type WHERE `filter_id` = :filter",
                ['range' => $filter['set_id'], 'type' => CourseSet::class, 'filter' => $filter['filter_id']]
            );
        }
    }

    protected function down()
    {
        $tables = [
            'massmail_messages',
            'massmail_permissions',
            'massmail_permission_degree',
            'massmail_permission_subject',
            'massmail_permission_institute',
            'massmail_filter',
            'massmail_tokens',
            'massmail_markers'
        ];
        DBManager::get()->execute(
            "DROP TABLE IF EXISTS `" . implode('`,`', $tables) . "`");

        $id = RolePersistence::getRoleIdByName(\MassMail\MassMailPermission::MASSMAIL_ROOT_ROLE);
        if (!empty($id)) {
            RolePersistence::deleteRole(new Role($id));
        }

        DBManager::get()->execute(
            "DELETE FROM `config_values` WHERE `field` = :field",
            ['field' => 'MASSMAIL_LECTURER_SEM_CATEGORIES']
        );
        DBManager::get()->execute(
            "DELETE FROM `config` WHERE `field` = :field",
            ['field' => 'MASSMAIL_LECTURER_SEM_CATEGORIES']
        );

        SendMassmailsJob::unregister();

        if ($this->columnExists('userfilter', 'range_id') && $this->columnExists('userfilter', 'range_type')) {
            DBManager::get()->exec("ALTER TABLE `userfilter` DROP `range_id`, DROP `range_type`");
        }
    }
};
