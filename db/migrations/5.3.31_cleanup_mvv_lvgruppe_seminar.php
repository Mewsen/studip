<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/28
 */
final class CleanupMvvLvgruppeSeminar extends Migration
{
    public function description()
    {
        return 'Removed orphaned course ids from table mvv_lvgruppe_seminar';
    }

    public function up()
    {
        $query = "DELETE FROM `mvv_lvgruppe_seminar`
                  WHERE `seminar_id` NOT IN (
                    SELECT `Seminar_id`
                    FROM `seminare`
                  )";
        DBManager::get()->exec($query);
    }
}
