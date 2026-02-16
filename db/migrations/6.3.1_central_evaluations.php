<?php


class CentralEvaluations extends Migration
{
    public function description()
    {
        return 'Adds new roles (Zentraler Evaluationsadmin, Einrichtungsbezogener Evaluationsadmin) and the possibility of evaluation courses with questionnaires.';
    }

    protected function up()
    {
        DBManager::get()->exec("
            ALTER TABLE `questionnaires`
            ADD COLUMN `is_template` tinyint(1) NOT NULL DEFAULT 0 AFTER `user_id`,
            ADD COLUMN `template_is_enabled` tinyint(1) DEFAULT NULL AFTER `is_template`,
            ADD COLUMN `template_id` char(32) DEFAULT NULL AFTER `questionnaire_id`,
            ADD KEY `template_id` (`template_id`)
        ");

        DBManager::get()->exec("
            CREATE TABLE `questionnaire_eval_central_profiles` (
                `semester_id` char(32) NOT NULL,
                `template_id` char(32) NOT NULL,
                `optional_templates` text DEFAULT NULL,
                `startdate` int(11) NOT NULL,
                `stopdate` int(11) NOT NULL,
                `anonymous` tinyint(1) NOT NULL DEFAULT 1,
                `editanswers` tinyint(1) NOT NULL DEFAULT 0,
                `resultvisibility` enum('never','afterending','afterparticipation') NOT NULL DEFAULT 'never',
                `result_visible_for` enum('autor','tutor','dozent') DEFAULT NULL,
                `minimum_responses` int(11) unsigned NOT NULL,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`semester_id`)
            ) ENGINE=InnoDB
        ");

        DBManager::get()->exec("
            CREATE TABLE `questionnaire_eval_assignments` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `template_id` char(32) DEFAULT NULL,
                `questionnaire_id` char(32) DEFAULT NULL,
                `semester_id` char(32) NOT NULL,
                `course_id` char(32) NOT NULL,
                `applied` tinyint(1) NOT NULL DEFAULT 1,
                `course_metadata` text DEFAULT NULL,
                `institute_id` char(32) NOT NULL,
                `startdate` int(11) DEFAULT NULL,
                `stopdate` int(11) DEFAULT NULL,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `questionnaire_id` (`questionnaire_id`),
                KEY `course_id` (`course_id`),
                KEY `template_id` (`template_id`),
                KEY `semester_id` (`semester_id`),
                KEY `institute_id` (`institute_id`)
            ) ENGINE=InnoDB
        ");

        DBManager::get()->exec("
            ALTER TABLE `questionnaire_questions`
            ADD COLUMN `template_question_id` char(32) DEFAULT NULL AFTER `question_id`,
            ADD KEY `template_question_id` (`template_question_id`)
        ");

        DBManager::get()->exec("
            INSERT INTO `roles` (`rolename`, `system`)
            VALUES ('Zentraler Evaluationsadmin', 'n'),
            ('Einrichtungsbezogener Evaluationsadmin', 'n')
        ");

        DBManager::get()->exec("INSERT INTO plugins (pluginclassname, pluginname, plugintype, enabled, navigationpos)
            VALUES ('CoreEvaluation', 'Evaluation', 'CorePlugin,StudipModule', 'yes', 1)
        ");

        $statement = DBManager::get()->prepare("
            INSERT INTO roles_plugins (roleid, pluginid)
            SELECT roleid, ?
            FROM roles
            WHERE `system` = 'y'
        ");
        $plugin_id = DBManager::get()->lastInsertId();
        $statement->execute([$plugin_id]);

        $get_sem_classes = DBManager::get()->prepare("
            SELECT *
            FROM `sem_classes`
        ");
        $get_sem_classes->execute();
        $update_modules = DBManager::get()->prepare("
            UPDATE `sem_classes` SET `modules` = :modules WHERE `id` = :sem_class
        ");
        while ($row = $get_sem_classes->fetch(PDO::FETCH_ASSOC)) {
            $json = json_decode($row['modules'], true);
            //evaluations are always sticky - either they are always active or always inactive
            $json['CoreEvaluation'] = [
                'activated' => $row['studygroup_mode'] ? 0 : 1,
                'sticky' => 1
            ];

            $update_modules->execute([
                'sem_class' => $row['id'],
                'modules' => json_encode($json)
            ]);
        }

        RolePersistence::expireRolesCache();
    }

    protected function down()
    {
        $get_sem_classes = DBManager::get()->prepare("
            SELECT *
            FROM `sem_classes`
        ");
        $get_sem_classes->execute();
        $update_modules = DBManager::get()->prepare("
            UPDATE `sem_classes` SET `modules` = :modules WHERE `id` = :sem_class
        ");
        while ($row = $get_sem_classes->fetch(PDO::FETCH_ASSOC)) {
            $json = json_decode($row['modules'], true);
            unset($json['CoreEvaluation']);

            $update_modules->execute([
                'sem_class' => $row['id'],
                'modules' => json_encode($json)
            ]);
        }

        $plugin_id = DBManager::get()->fetchColumn("
            SELECT `pluginid` from `plugins` WHERE `pluginclassname` = 'CoreEvaluation'
        ");
        $roles_plugins_statement = DBManager::get()->prepare("
            DELETE FROM `roles_plugins` WHERE pluginid = ?
        ");
        $roles_plugins_statement->execute([$plugin_id]);

        DBManager::get()->exec("
            DELETE FROM `plugins` WHERE `pluginclassname` = 'CoreEvaluation'
        ");

        DBManager::get()->exec("
            DELETE FROM `roles`
            WHERE (`rolename` = 'Zentraler Evaluationsadmin' OR `rolename` = 'Einrichtungsbezogener Evaluationsadmin')
                AND `system` = 'n'
        ");

        DBManager::get()->exec("
            ALTER TABLE `questionnaires`
            DROP COLUMN `is_template`,
            DROP COLUMN `template_is_enabled`,
            DROP COLUMN `template_id`
        ");

        DBManager::get()->exec("
            DROP TABLE `questionnaire_eval_assignments`
        ");
        DBManager::get()->exec("
            DROP TABLE `questionnaire_eval_central_profiles`
        ");

        DBManager::get()->exec("
            ALTER TABLE `questionnaire_questions`
            DROP COLUMN `template_question_id`
        ");

        RolePersistence::expireRolesCache();
    }
}
