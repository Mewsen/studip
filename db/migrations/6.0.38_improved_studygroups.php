<?php

class ImprovedStudygroups extends Migration
{

    public function description()
    {
        return 'Improve studygroups.';
    }

    public function up()
    {
        DBManager::get()->exec("
            CREATE TABLE `studygroup_courses` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `studygroup_id` char(32) NOT NULL,
                `course_id` char(32) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `studygroup_id` (`studygroup_id`,`course_id`),
                KEY `studygroup_id_2` (`studygroup_id`),
                KEY `course_id` (`course_id`)
            )
        ");
        DBManager::get()->exec("
            CREATE TABLE `studygroup_courses_proposals` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `studygroup_id` char(32) NOT NULL,
                `course_id` char(32) NOT NULL,
                `proposed_from` enum('course','studygroup') NOT NULL,
                `user_id` char(32) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `studygroup_id` (`studygroup_id`,`course_id`),
                KEY `course_id` (`course_id`),
                KEY `studygroup_id_2` (`studygroup_id`)
            )
        ");
        DBManager::get()->exec("
            CREATE TABLE `tags` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(128) NOT NULL,
                `active` tinyint(1) DEFAULT 1,
                `chdate` int(11) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
        ");
        DBManager::get()->exec("
            CREATE TABLE `tags_relations` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `tag_id` int(11) DEFAULT NULL,
                `range_id` varchar(32) DEFAULT NULL,
                `range_type` varchar(100) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `tag_id` (`tag_id`),
                KEY `range_id` (`range_id`),
                KEY `range_type` (`range_type`)
            )
        ");
        DBManager::get()->exec("
            ALTER TABLE `seminare`
            ADD COLUMN `expires` int(11) DEFAULT NULL
        ");
        DBManager::get()->exec("
            CREATE TABLE `studygroup_stgteil` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `studygroup_id` char(32) NOT NULL,
                `stgteil_id` varchar(32) NULL NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `studygroup_id` (`studygroup_id`,`stgteil_id`),
                KEY `studygroup_id_2` (`studygroup_id`),
                KEY `stgteil_id` (`stgteil_id`)
            )
        ");
        DBManager::get()->exec(
            "INSERT IGNORE INTO `config`
             (`field`, `type`, `range`, `value`, `section`, `description`, `mkdate`, `chdate`)
             VALUES
             (
                 'STUDYGROUP_ON_STGTEIL_ENABLE', 'boolean', 'global', '1', 'studygroups', 'Are studygroups allowed to get attached to study course parts?',
                 UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
             )"
        );
        DBManager::get()->exec("
            INSERT INTO `admissionrules` (`ruletype`, `active`, `mkdate`, `path`)
            VALUES
	            ('ConnectedcourseAdmission', 1, UNIX_TIMESTAMP(), 'lib/admissionrules/connectedcourseadmission');
        ");


        $statement = DBManager::get()->prepare("
            SELECT *
            FROM config
            WHERE field = 'GLOBALSEARCH_MODULES'
        ");
        $statement->execute();
        $config = $statement->fetch(PDO::FETCH_ASSOC);
        $config['value'] = json_decode($config['value'], true);
        $config['value']['GlobalSearchStudygroups'] = [
            'order' => 15,
            'active' => true,
            'fulltext' => false
        ];

        //Adding to the global search:

        $statement = DBManager::get()->prepare("
            UPDATE config
            SET `value` = :json
            WHERE field = 'GLOBALSEARCH_MODULES'
        ");
        $statement->execute([
            'json' => json_encode($config['value'])
        ]);

        $statement = DBManager::get()->prepare("
            SELECT *
            FROM config_values
            WHERE field = 'GLOBALSEARCH_MODULES'
        ");
        $statement->execute();
        $config = $statement->fetch(PDO::FETCH_ASSOC);
        if ($config) {
            $config['value'] = json_decode($config['value'], true);
            $config['value']['GlobalSearchStudygroups'] = [
                'order' => 15,
                'active' => true,
                'fulltext' => true
            ];

            $statement = DBManager::get()->prepare("
                UPDATE config_values
                SET `value` = :json
                WHERE field = 'GLOBALSEARCH_MODULES'
            ");
            $statement->execute([
                'json' => json_encode($config['value'])
            ]);
        }

        $db = DBManager::get();

        // get position
        $pos = $db->fetchColumn("SELECT MAX(navigationpos) + 1 FROM plugins WHERE plugintype = 'PortalPlugin'");

        // install as portal plugin
        $sql = "INSERT INTO plugins (pluginclassname, pluginname, plugintype, enabled, navigationpos) VALUES (?)";
        $db->execute($sql, [['StudygroupWidget', 'StudygroupWidget', 'PortalPlugin', 'yes', $pos]]);

        $sql = "INSERT INTO roles_plugins (roleid, pluginid)
                SELECT roleid, ? FROM roles WHERE `system` = 'y' AND rolename != 'Nobody'";
        $db->execute($sql, [$db->lastInsertId()]);


        // Add default cron tasks and schedules
        $new_job = [
            'filename' => 'lib/cronjobs/studygroup_expiration.php',
            'class'    => StudygroupExpirationJob::class,
        ];

        $query = "INSERT IGNORE INTO `cronjobs_tasks`
                    (`task_id`, `filename`, `class`, `active`)
                  VALUES (:task_id, :filename, :class, 1)";
        $task_statement = DBManager::get()->prepare($query);

        $query = "INSERT IGNORE INTO `cronjobs_schedules`
                    (`schedule_id`, `task_id`, `parameters`,
                     `minute`, `hour`, `mkdate`, `chdate`,
                     `last_result`)
                  VALUES (:schedule_id, :task_id, '[]',
                          :minute, :hour, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                          NULL)";
        $schedule_statement = DBManager::get()->prepare($query);


        $task_id = md5(uniqid('expirestudygroups', true));

        $task_statement->execute([
            ':task_id'  => $task_id,
            ':filename' => $new_job['filename'],
            ':class'    => $new_job['class'],
        ]);

        $schedule_id = md5(uniqid('schedule', true));
        $schedule_statement->execute([
            ':schedule_id' => $schedule_id,
            ':task_id'     => $task_id,
            ':hour'        => $new_job['hour'],
            ':minute'      => $new_job['minute'],
        ]);

        // get position
        $pos = $db->fetchColumn("SELECT MAX(navigationpos) + 1 FROM plugins WHERE plugintype = 'PortalPlugin'");

        // install as portal plugin
        $sql = "INSERT INTO plugins (pluginclassname, pluginname, plugintype, enabled, navigationpos) VALUES (?)";
        $db->execute($sql, [['MyStudygroupsWidget', 'MyStudygroupsWidget', 'PortalPlugin', 'yes', $pos]]);

        $sql = "INSERT INTO roles_plugins (roleid, pluginid)
                SELECT roleid, ? FROM roles WHERE `system` = 'y' AND rolename != 'Nobody'";
        $db->execute($sql, [$db->lastInsertId()]);
    }

    public function down()
    {
        $db = DBManager::get();

        $plugin_id = $db->fetchColumn('SELECT pluginid FROM plugins WHERE pluginclassname = ?', ['MyStudygroupsWidget']);

        $db->execute('DELETE FROM widget_default WHERE pluginid = ?', [$plugin_id]);
        $db->execute('DELETE FROM widget_user WHERE pluginid = ?', [$plugin_id]);
        $db->execute('DELETE FROM roles_plugins WHERE pluginid = ?', [$plugin_id]);
        $db->execute('DELETE FROM plugins WHERE pluginid = ?', [$plugin_id]);

        $db->exec("
            DELETE `cronjobs_schedules`.* FROM `cronjobs_schedules`
            INNER JOIN `cronjobs_tasks` USING (`task_id`)
                   WHERE `cronjobs_tasks`.`class` = 'StudygroupExpirationJob'
        ");
        $db->exec("
            DELETE FROM `cronjobs_tasks`
            WHERE `cronjobs_tasks`.`class` = 'StudygroupExpirationJob'
        ");

        $plugin_id = $db->fetchColumn('SELECT pluginid FROM plugins WHERE pluginclassname = ?', ['StudygroupWidget']);

        $db->execute('DELETE FROM widget_default WHERE pluginid = ?', [$plugin_id]);
        $db->execute('DELETE FROM widget_user WHERE pluginid = ?', [$plugin_id]);
        $db->execute('DELETE FROM roles_plugins WHERE pluginid = ?', [$plugin_id]);
        $db->execute('DELETE FROM plugins WHERE pluginid = ?', [$plugin_id]);

        $statement = DBManager::get()->prepare("
            SELECT *
            FROM config_values
            WHERE field = 'GLOBALSEARCH_MODULES'
        ");
        $statement->execute();
        $config = $statement->fetch(PDO::FETCH_ASSOC);
        if ($config) {
            $config['value'] = json_decode($config['value'], true);
            unset($config['value']['GlobalSearchStudygroups']);
            $statement = DBManager::get()->prepare("
                UPDATE config_values
                SET `value` = :json
                WHERE field = 'GLOBALSEARCH_MODULES'
            ");
            $statement->execute([
                'json' => json_encode($config['value'])
            ]);
        }

        $statement = DBManager::get()->prepare("
            SELECT *
            FROM config
            WHERE field = 'GLOBALSEARCH_MODULES'
        ");
        $statement->execute();
        $config = $statement->fetch(PDO::FETCH_ASSOC);
        $config['value'] = json_decode($config['value'], true);
        unset($config['value']['GlobalSearchStudygroups']);
        $statement = DBManager::get()->prepare("
            UPDATE config
            SET `value` = :json
            WHERE field = 'GLOBALSEARCH_MODULES'
        ");
        $statement->execute([
            'json' => json_encode($config['value'])
        ]);


        DBManager::get()->exec("
            DELETE FROM `admissionrules` WHERE `ruletype` = 'ConnectedcourseAdmission'
        ");
        DBManager::get()->exec("
            DELETE `config`, `config_values`
            FROM `config`
                LEFT JOIN `config_values` USING (`field`)
            WHERE `config`.`field` = 'STUDYGROUP_ON_STGTEIL_ENABLE'
        ");
        DBManager::get()->exec("
            DROP TABLE `studygroup_stgteil`
        ");
        DBManager::get()->exec("
            ALTER TABLE `seminare`
            DROP COLUMN `expires`
        ");
        DBManager::get()->exec("
            DROP TABLE `tags_relations`
        ");
        DBManager::get()->exec("
            DROP TABLE `tags`
        ");
        DBManager::get()->exec("
            DROP TABLE `studygroup_courses_proposals`
        ");
        DBManager::get()->exec("
            DROP TABLE `studygroup_courses`
        ");
    }

}
