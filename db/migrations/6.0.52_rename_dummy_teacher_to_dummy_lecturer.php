<?php
final class RenameDummyTeacherToDummyLecturer extends Migration
{
    public function description()
    {
        return 'Renames configuration DUMMY_TEACHER_ID to DUMMY_LECTURER_ID';
    }

    public function up()
    {
        $query = "UPDATE `config`
                  SET `field` = 'DUMMY_LECTURER_ID'
                  WHERE `field` = 'DUMMY_TEACHER_ID'";
        DBManager::get()->exec($query);

        $query = "UPDATE `config_values`
                  SET `field` = 'DUMMY_LECTURER_ID'
                  WHERE `field` = 'DUMMY_TEACHER_ID'";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "UPDATE `config_values`
                  SET `field` = 'DUMMY_TEACHER_ID'
                  WHERE `field` = 'DUMMY_LECTURER_ID'";
        DBManager::get()->exec($query);

        $query = "UPDATE `config`
                  SET `field` = 'DUMMY_TEACHER_ID'
                  WHERE `field` = 'DUMMY_LECTURER_ID'";
        DBManager::get()->exec($query);
    }
}
