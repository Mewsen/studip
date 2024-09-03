<?php
return new class extends Migration
{

    public function description()
    {
        return 'StEP 4110: Remove SOAP/XMLRPC webservices';
    }

    protected function up()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE `webservice_access_rules`");
        $db->exec("DELETE FROM `config` WHERE `field` = 'WEBSERVICES_ENABLE'");
        $db->exec("DELETE FROM `config_values` WHERE `field` = 'WEBSERVICES_ENABLE'");
    }

    protected function down()
    {
    }
};
