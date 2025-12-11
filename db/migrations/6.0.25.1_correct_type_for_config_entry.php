<?php
return new class extends Migration
{

    public function description()
    {
        return 'Corrects the config entry type to something the database accepts.';
    }

    protected function up()
    {
        DBManager::get()->exec("UPDATE `config` SET `type` = 'boolean' WHERE `field` = 'ENABLE_NUMBER_OF_PARTICIPANTS'");
    }
};
