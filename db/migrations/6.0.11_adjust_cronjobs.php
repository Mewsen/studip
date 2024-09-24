<?php
return new class extends Migration
{
    private const ADJUSTMENTS = [
        'lib/cronjobs/purge_cache',
        'lib/cronjobs/check_admission',
        'lib/cronjobs/session_gc',
        'lib/cronjobs/cleanup_log',
        'lib/cronjobs/garbage_collector',
        'lib/cronjobs/send_mail_queue',
        'lib/cronjobs/remind_oer_upload',
        'lib/cronjobs/send_mail_notifications',
    ];

    public function description()
    {
        return 'Adjusts the class names for core cronjobs by losing the .class suffix';
    }

    protected function up()
    {
        $this->changeCronjobFilenames('.class.php', '.php');
    }

    protected function down()
    {
        $this->changeCronjobFilenames('.php', '.class.php');
    }

    private function changeCronjobFilenames(string $fromExtension, string $toExtension): void
    {
        $query = "UPDATE `cronjobs_tasks`
                  SET `filename` = :new
                  WHERE `filename` = :old";
        $statement = DBManager::get()->prepare($query);

        foreach (self::ADJUSTMENTS as $filename) {
            $statement->bindValue(':new', $filename . $toExtension);
            $statement->bindValue(':old', $filename . $fromExtension);
            $statement->execute();
        }
    }
};
