<?php

class Step5675RunningProcessesWidget extends Migration
{
    protected function up()
    {
        $pos = DBManager::get()->fetchColumn("SELECT MAX(navigationpos) + 1 FROM plugins WHERE plugintype = 'PortalPlugin'");
        $sql = "INSERT INTO plugins (pluginclassname, pluginname, plugintype, enabled, navigationpos) VALUES (?)";
        DBManager::get()->execute($sql, [['RunningProcessesWidget', 'RunningProcessesWidget', 'PortalPlugin', 'yes', $pos]]);

        $sql = "INSERT INTO roles_plugins (roleid, pluginid)
                SELECT roleid, ? FROM roles WHERE `system` = 'y' AND rolename != 'Nobody'";
        DBManager::get()->execute($sql, [DBManager::get()->lastInsertId()]);
    }

}
