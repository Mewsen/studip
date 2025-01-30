<?php
return new class extends Migration {
    public function description()
    {
        return 'Removes orphaned entries in table "studygroup_invitations"';
    }

    protected function up()
    {
        $query = "DELETE FROM `studygroup_invitations`
                  WHERE `user_id` NOT IN (
                      SELECT `user_id` FROM `auth_user_md5`
                  ) OR `sem_id` NOT IN (
                      SELECT `Seminar_id` FROM `seminare`
                  )";
        DBManager::get()->exec($query);
    }
};
