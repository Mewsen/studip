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

        // remind only on reminder date
        //RemindOerUpload::register()->schedulePeriodic(00,01)->activate();

        // Add default cron tasks and schedules
        $new_job = [
            'filename'    => 'lib/cronjobs/remind_oer_upload.class.php',
            'class'       => 'RemindOerUpload',
            'priority'    => 'normal',
            'minute'      => '0',
            'hour'        => '1',
            'active'      => '1'
        ];

        $query = "INSERT IGNORE INTO `cronjobs_tasks`
                    (`task_id`, `filename`, `class`, `active`)
                  VALUES (:task_id, :filename, :class, 1)";
        $task_statement = DBManager::get()->prepare($query);

        $query = "INSERT IGNORE INTO `cronjobs_schedules`
                    (`schedule_id`, `task_id`, `parameters`, `priority`,
                     `type`, `minute`, `hour`, `mkdate`, `chdate`,
                     `last_result`, `active`)
                  VALUES (:schedule_id, :task_id, '[]', :priority, 'periodic',
                          :minute, :hour, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
                          NULL, :active)";
        $schedule_statement = DBManager::get()->prepare($query);


        $task_id = md5(uniqid('task', true));

        $task_statement->execute([
            ':task_id'  => $task_id,
            ':filename' => $new_job['filename'],
            ':class'    => $new_job['class'],
        ]);

        $schedule_id = md5(uniqid('schedule', true));
        $schedule_statement->execute([
            ':schedule_id' => $schedule_id,
            ':task_id'     => $task_id,
            ':priority'    => $new_job['priority'],
            ':hour'        => $new_job['hour'],
            ':minute'      => $new_job['minute'],
            ':active'      => $new_job['active']
        ]);

    }

    public function down()
    {
        $query = "DROP TABLE `oer_post_upload`";
        DBManager::get()->exec($query);

        $query = "DELETE FROM `cronjobs_tasks` WHERE `class` = 'RemindOerUpload'";
        DBManager::get()->exec($query);

    }

}
