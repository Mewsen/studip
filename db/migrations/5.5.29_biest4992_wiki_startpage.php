<?php

class Biest4992WikiStartpage extends Migration
{
    public function description()
    {
        return 'Changes the range of the WIKI_STARTPAGE_ID config to match courses and institutes.';
    }
    public function up()
    {
        DBManager::get()->exec("
            UPDATE `config`
            SET `range` = 'range'
            WHERE `field` = 'WIKI_STARTPAGE_ID';
        ");
    }

    public function down()
    {
        DBManager::get()->exec("
            UPDATE `config`
            SET `range` = 'course'
            WHERE `field` = 'WIKI_STARTPAGE_ID';
        ");
    }
}
