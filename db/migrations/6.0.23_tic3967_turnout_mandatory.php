<?php

final class Tic3967TurnoutMandatory extends Migration
{
    public function description()
    {
        return 'adds option to make admission turnout mandatory';
    }

    public function up()
    {
        DBManager::get()->exec("
            ALTER TABLE `sem_classes` ADD `admission_turnout_mandatory` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `is_group`
        ");
        $cache = StudipCacheFactory::getCache();
        $cache->expire('DB_SEM_CLASSES_ARRAY');
    }

    public function down()
    {
        DBManager::get()->exec("ALTER TABLE `sem_classes` DROP `admission_turnout_mandatory`");
        $cache = StudipCacheFactory::getCache();
        $cache->expire('DB_SEM_CLASSES_ARRAY');
    }
}
