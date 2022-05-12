<?php
class AddOerPostUploadTable extends Migration
{
    public function description()
    {
        return "Adds table to create oer upload reminders";
    }

    public function up()
    {
        $db = DBmanager::Get();

        $db->exec("CREATE TABLE IF NOT EXISTS `oer_post_upload` (
                `file_ref_id` char(32),
                `user_id` char(32),
                `reminder_date` int unsigned,
                `mkdate` int(11) NOT NULL,
                `chdate` int(11) NOT NULL,
                PRIMARY KEY (`user_id`, `file_ref_id`)
            )
            ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC
            ");

    }

    public function down()
    {
        $query = "DROP TABLE `oer_post_upload`";
        DBManager::get()->exec($query);
    }

}
