<?php

class VipsStatusgruppeUser extends Migration
{
    public function description()
    {
        return 'add missing entries in statusgruppe_user for vips migration';
    }

    public function up()
    {
        $db = DBManager::get();

        // the inital migration set these to 0
        $sql = "UPDATE etask_assignments SET end = NULL WHERE type = 'selftest' AND end = 0";
        $db->exec($sql);

        // the inital migration set these to 0
        $sql = 'UPDATE etask_group_members SET end = NULL WHERE end = 0';
        $db->exec($sql);

        // the inital migration did not add entries to statusgruppe_user
        $sql = 'INSERT IGNORE INTO statusgruppe_user (statusgruppe_id, user_id, mkdate)
                SELECT group_id, user_id, start FROM etask_group_members WHERE end IS NULL';
        $db->exec($sql);
    }
}
