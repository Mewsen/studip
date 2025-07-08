<?php

final class AddCalendarWidget extends Migration
{
    public function up()
    {
        $db = DBManager::get();

        // get position
        $navigationpos = $db->fetchColumn("SELECT MAX(navigationpos) + 1 FROM plugins WHERE plugintype = 'PortalPlugin'");

        // install as portal plugin
        $db->execute(
            "INSERT INTO plugins (pluginclassname, pluginname, plugintype, enabled, navigationpos)
             VALUES (:pluginclassname, :pluginname, :plugintype, :enabled, :navigationpos)",
            [
                'pluginclassname' => 'CalendarWidget',
                'pluginname' => 'CalendarWidget',
                'plugintype' => 'PortalPlugin',
                'enabled' => 'yes',
                'navigationpos' => $navigationpos
            ]
        );

        $db->execute(
            "INSERT INTO roles_plugins (roleid, pluginid)
             SELECT roleid, :pluginid
             FROM roles
             WHERE `system` = 'y' AND rolename != 'Nobody'",
            ['pluginid' => $db->lastInsertId()]
        );
    }

    public function down()
    {
        $db = DBManager::get();

        $plugin_id = $db->fetchColumn(
            "SELECT pluginid FROM plugins WHERE pluginclassname = ?", ['CalendarWidget']
        );

        $db->execute("DELETE FROM widget_default WHERE pluginid = ?", [$plugin_id]);
        $db->execute("DELETE FROM widget_user WHERE pluginid = ?", [$plugin_id]);
        $db->execute("DELETE FROM roles_plugins WHERE pluginid = ?", [$plugin_id]);
        $db->execute("DELETE FROM plugins WHERE pluginid = ?", [$plugin_id]);
    }

}
