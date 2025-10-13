<?php
final class RenameStudygroupExpirationCronjob extends Migration
{
    public function description()
    {
        return 'Removes .class from registered cronjob filename that expires studygroups';
    }

    protected function up()
    {
        $query = "UPDATE `cronjobs_tasks`
                  SET `filename` = REPLACE(`filename`, '.class.php', '.php')
                  WHERE `class` = 'StudygroupExpirationJob'";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        // No down migration since the filename was not right in the first place
    }
}
