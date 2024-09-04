<?php
return new class extends Migration
{

    public function description()
    {
        return 'StEP 4109: Remove ELEARNING_INTERFACE';
    }

    protected function up()
    {
        $db = DBManager::get();

        $plugin_id = $db->fetchColumn("SELECT pluginid FROM plugins WHERE pluginclassname='CoreElearningInterface'");
        $db->execute("DELETE FROM roles_plugins WHERE pluginid=?", [$plugin_id]);
        $db->execute("DELETE FROM tools_activated WHERE plugin_id=?", [$plugin_id]);
        $db->execute("DELETE FROM plugins WHERE pluginid=?", [$plugin_id]);

        $ilias_config = $db->fetchColumn("SELECT `value` FROM config_values WHERE `field`='ILIAS_INTERFACE_SETTINGS'");
        if ($ilias_config) {
            $ilias_config = json_decode($ilias_config, true);
            if (is_array($ilias_config)) {
                $config_keys = array_keys($ilias_config);
                if (count($config_keys)) {
                    $db->execute("DELETE FROM auth_extern WHERE external_user_system_type NOT IN (?)", [$config_keys]);
                    $db->execute("DELETE FROM object_contentmodules WHERE system_type NOT IN (?)", [$config_keys]);
                }
            }
        } else {
            $elearning_active = $db->fetchColumn("SELECT `value` FROM config_values WHERE `field` LIKE 'ELEARNING_INTERFACE%ACTIVE' LIMIT 1");
            if (!$elearning_active) {
                $db->execute("DELETE FROM auth_extern");
                $db->execute("DELETE FROM object_contentmodules");
            }
        }
        $db->exec("DELETE FROM `config_values` WHERE `field` LIKE 'ELEARNING_INTERFACE%'");
        $db->exec("DELETE FROM `config_values` WHERE `field` IN ('SOAP_ENABLE', 'SOAP_USE_PHP5')");
        $db->exec("DELETE FROM `config` WHERE `field` LIKE 'ELEARNING_INTERFACE%'");
        $db->exec("DELETE FROM `config` WHERE `field` IN ('SOAP_ENABLE', 'SOAP_USE_PHP5')");

    }

    protected function down()
    {
    }

};
