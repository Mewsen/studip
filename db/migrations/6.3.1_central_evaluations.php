<?php


class CentralEvaluations extends Migration
{
    public function description()
    {
        return 'Adds a new role (Zentrale Evaluationsadministration) and the possibility of evaluation courses with questionnaires. ';
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
            VALUES ('Zentrale Evaluationsadministration', 'n');
        ");
        RolePersistence::expireRolesCache();
    }

    protected function down()
    {
        DBManager::get()->execute("
            DELETE FROM `roles` WHERE `rolename` = 'Zentrale Evaluationsadministration' AND `system` = 'n'
        ");
        DBManager::get()->exec("
            ALTER TABLE `questionnaire`
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
