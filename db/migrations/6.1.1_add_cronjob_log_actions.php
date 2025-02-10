<?php

class AddCronjobLogActions extends Migration
{

    public function description ()
    {
        return 'Adds log actions for cronjobs deleting, activating and deactivating.';
    }

    public function up()
    {
        DBManager::get()->exec("
            INSERT INTO `log_actions`
            SET `action_id` = MD5('CRONJOB_TASK_DELETED'),
                `name` = 'CRONJOB_TASK_DELETED',
                `description` = 'Cronjob-Aufgabe wurde gelöscht.',
                `info_template` = '%user löscht Cronjob-Aufgabe %info.',
                `active` = 1,
                `expires` = 0,
                `type` = 'core',
                `mkdate` = UNIX_TIMESTAMP(),
                `chdate` = UNIX_TIMESTAMP()
        ");
        DBManager::get()->exec("
            INSERT INTO `log_actions`
            SET `action_id` = MD5('CRONJOB_SCHEDULE_DELETED'),
                `name` = 'CRONJOB_SCHEDULE_DELETED',
                `description` = 'Cronjob wurde gelöscht.',
                `info_template` = '%user löscht Cronjob %info.',
                `active` = 1,
                `expires` = 0,
                `type` = 'core',
                `mkdate` = UNIX_TIMESTAMP(),
                `chdate` = UNIX_TIMESTAMP()
        ");
        DBManager::get()->exec("
            INSERT INTO `log_actions`
            SET `action_id` = MD5('CRONJOB_TASK_DEACTIVATED'),
                `name` = 'CRONJOB_TASK_DEACTIVATED',
                `description` = 'Cronjob-Aufgabe wurde deaktiviert.',
                `info_template` = '%user deaktiviert Cronjob-Aufgabe %info.',
                `active` = 1,
                `expires` = 0,
                `type` = 'core',
                `mkdate` = UNIX_TIMESTAMP(),
                `chdate` = UNIX_TIMESTAMP()
        ");
        DBManager::get()->exec("
            INSERT INTO `log_actions`
            SET `action_id` = MD5('CRONJOB_SCHEDULE_DEACTIVATED'),
                `name` = 'CRONJOB_SCHEDULE_DEACTIVATED',
                `description` = 'Cronjob wurde deaktiviert.',
                `info_template` = '%user deaktiviert Cronjob %info.',
                `active` = 1,
                `expires` = 0,
                `type` = 'core',
                `mkdate` = UNIX_TIMESTAMP(),
                `chdate` = UNIX_TIMESTAMP()
        ");
        DBManager::get()->exec("
            INSERT INTO `log_actions`
            SET `action_id` = MD5('CRONJOB_TASK_ACTIVATED'),
                `name` = 'CRONJOB_TASK_ACTIVATED',
                `description` = 'Cronjob-Aufgabe wurde aktiviert.',
                `info_template` = '%user aktiviert Cronjob-Aufgabe %info.',
                `active` = 1,
                `expires` = 0,
                `type` = 'core',
                `mkdate` = UNIX_TIMESTAMP(),
                `chdate` = UNIX_TIMESTAMP()
        ");
        DBManager::get()->exec("
            INSERT INTO `log_actions`
            SET `action_id` = MD5('CRONJOB_SCHEDULE_ACTIVATED'),
                `name` = 'CRONJOB_SCHEDULE_ACTIVATED',
                `description` = 'Cronjob wurde aktiviert.',
                `info_template` = '%user aktiviert Cronjob %info.',
                `active` = 1,
                `expires` = 0,
                `type` = 'core',
                `mkdate` = UNIX_TIMESTAMP(),
                `chdate` = UNIX_TIMESTAMP()
        ");
    }

    public function down()
    {
        DBManager::get()->exec("
            DELETE FROM `log_actions`
            WHERE `name` = 'CRONJOB_TASK_DELETED'
        ");
        DBManager::get()->exec("
            DELETE FROM `log_actions`
            WHERE `name` = 'CRONJOB_SCHEDULE_DELETED'
        ");
        DBManager::get()->exec("
            DELETE FROM `log_actions`
            WHERE `name` = 'CRONJOB_TASK_DEACTIVATED'
        ");
        DBManager::get()->exec("
            DELETE FROM `log_actions`
            WHERE `name` = 'CRONJOB_SCHEDULE_DEACTIVATED'
        ");
        DBManager::get()->exec("
            DELETE FROM `log_actions`
            WHERE `name` = 'CRONJOB_TASK_ACTIVATED'
        ");
        DBManager::get()->exec("
            DELETE FROM `log_actions`
            WHERE `name` = 'CRONJOB_SCHEDULE_ACTIVATED'
        ");
    }
}
