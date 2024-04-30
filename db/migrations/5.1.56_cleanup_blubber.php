<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4097
 */
return new class extends Migration
{
    public function description()
    {
        return 'Removes orhpaned blubber entries';
    }

    protected function up()
    {
        $query = "DELETE bt, ouv
                  FROM `blubber_threads` AS bt
                  LEFT JOIN `object_user_visits` AS ouv
                    ON (bt.`thread_id` = ouv.`object_id`)
                  WHERE bt.`external_contact` = 0
                    AND bt.`thread_id` != 'global'
                    AND bt.`user_id` != ''
                    AND bt.`user_id` NOT IN (
                        SELECT `user_id` FROM `auth_user_md5`
                    )";
        DBManager::get()->exec($query);

        $query = "DELETE FROM `blubber_comments`
                  WHERE (
                      `external_contact` = 0
                      AND `user_id` NOT IN (
                          SELECT `user_id` FROM `auth_user_md5`
                      )
                    ) OR `thread_id` NOT IN (
                      SELECT `thread_id` FROM `blubber_threads`
                    )";
        DBManager::get()->exec($query);

        $query = "DELETE FROM `blubber_mentions`
                  WHERE (
                      `external_contact` = 0
                      AND `user_id` NOT IN (
                          SELECT `user_id` FROM `auth_user_md5`
                      )
                    ) OR `thread_id` NOT IN (
                    SELECT `thread_id` FROM `blubber_threads`
                  )";
        DBManager::get()->exec($query);
    }
};
