<?php

final class RemoveOldEvaluation extends Migration
{
    public function description()
    {
        return 'removes old evaluation tables';
    }

    public function up()
    {
        DBManager::get()->exec('DROP TABLE `eval`');
        DBManager::get()->exec('DROP TABLE `eval_group_template`');
        DBManager::get()->exec('DROP TABLE `eval_range`');
        DBManager::get()->exec('DROP TABLE `eval_templates`');
        DBManager::get()->exec('DROP TABLE `eval_templates_eval`');
        DBManager::get()->exec('DROP TABLE `eval_templates_user`');
        DBManager::get()->exec('DROP TABLE `eval_user`');
        DBManager::get()->exec('DROP TABLE `evalanswer`');
        DBManager::get()->exec('DROP TABLE `evalanswer_user`');
        DBManager::get()->exec('DROP TABLE `evalgroup`');
        DBManager::get()->exec('DROP TABLE `evalquestion`');

        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE field IN (
                    'EVAL_AUSWERTUNG_GRAPH_FORMAT',
                    'EVAL_ENABLE', 'EVAL_AUSWERTUNG_CONFIG_ENABLE'
                  )";
        DBManager::get()->exec($query);
    }
}
