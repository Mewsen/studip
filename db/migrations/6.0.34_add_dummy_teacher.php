<?php
class AddDummyTeacher extends Migration
{
    public function description()
    {
        return 'Adds a dummy teacher (N.N.). Adds config to set dummy teachers id. @see tic #4289';
    }

    protected function up()
    {
        // Add dummy teacher
        $stm = DBManager::get()->prepare("SELECT * FROM `auth_user_md5` WHERE `username` = 'N.N.'");
        $stm->execute();
        if ($stm->rowCount() > 0) {
            $res = $stm->fetch();
            $user_id = $res['user_id'];
        } else {
            $user_id = '2afaa0dce05f0b12a7318075e52879e2';
            DBManager::get()->execute(
                "INSERT INTO `auth_user_md5` (user_id, username, perms, Vorname, Nachname, visible) VALUES (:user_id, :username, :perms, :Vorname, :Nachname, :visible)",
                [
                    'user_id' => $user_id,
                    'username' => 'N.N.',
                    'perms' => 'dozent',
                    'Vorname' => 'N.',
                    'Nachname' => 'N.',
                    'visible' => 'never'
                ]
            );
        }

        // Add config
        DBManager::get()->execute(
            "INSERT IGNORE INTO `config` VALUES (:field, :value, :type, :range, :section, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :desc)",
            [
                'field' => 'DUMMY_TEACHER_ID',
                'value' => $user_id,
                'type' => 'string',
                'range' => 'global',
                'section' => 'global',
                'desc' => 'ID of user that should be added to course if no teacher is left'
            ]
        );
    }

    protected function down()
    {
        // Dont delete dummy user, maybe it existed before this migration

        // Delete config
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'DUMMY_TEACHER_ID'";
        DBManager::get()->exec($query);
    }
}
