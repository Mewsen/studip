<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4777
 */
return new class extends Migration
{
    public function description()
    {
        return 'Removes the invalid entries from folder table (see BIEST#4008)';
    }

    protected function up()
    {
        $query = "DELETE FROM `folders`
                  WHERE `folder_type` IN ('course', 'user')";
        DBManager::get()->exec($query);
    }
};
