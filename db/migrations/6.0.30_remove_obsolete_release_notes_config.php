<?php

final class RemoveObsoleteReleaseNotesConfig extends Migration
{
    public function description()
    {
        return 'removes the obsolete global config entry SHOW_RELEASE_NOTES';
    }

    public function up()
    {
        DBManager::get()->exec("DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field`  = 'SHOW_RELEASE_NOTES'");
    }

}
