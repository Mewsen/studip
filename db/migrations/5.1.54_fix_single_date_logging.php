<?php

final class FixSingleDateLogging extends Migration
{
    public function description()
    {
        return 'Fix broken SingleDate-Logging';
    }

    public function up()
    {
        DBManager::get()->exec("UPDATE `log_actions` SET `info_template`= '%user hat in %sem(%affected) den Einzeltermin %singledate(%coaffected) geändert.' WHERE `name` = 'SINGLEDATE_CHANGE_TIME'");
        DBManager::get()->exec("UPDATE `log_actions` SET `info_template`= '%user hat in %sem(%affected) den Einzeltermin %singledate(%coaffected) hinzugefügt' WHERE `name` = 'SEM_ADD_SINGLEDATE'");

        DBManager::get()->exec("
            UPDATE `log_events`
            SET `dbg_info` = `info`, `info` = `coaffected_range_id`, `coaffected_range_id` = null
            WHERE `action_id` IN (
                SELECT `action_id` FROM `log_actions` WHERE `name` IN ('SINGLEDATE_CHANGE_TIME', 'SEM_ADD_SINGLEDATE')
            )
        ");
    }

}
