<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4131
 */
return new class extends Migration
{
    public function description()
    {
        return 'Removes the configuration for portable password hashes';
    }

    protected function up()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config` 
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'PHPASS_USE_PORTABLE_HASH'";
        DBManager::get()->exec($query);
    }
};
