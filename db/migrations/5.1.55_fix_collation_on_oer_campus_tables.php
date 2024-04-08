<?php

/**
 * This migration will set the columns of the oer campus to the correct state
 * regarding the collations of some columns. This should only be necessary if
 * the plugin "Lernmarktplatz" was installed before the migration of Stud.IP
 * that introduced the oer campus. In any other case the tables should not be
 * changed since they are already in the correct format.
 *
 * @see https://gitlab.studip.de/studip/studip/-/issues/3964
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 */
final class FixCollationOnOerCampusTables extends Migration
{
    public function description()
    {
        return 'Correct collations for oer campus tables';
    }

    protected function up()
    {
        $query = "ALTER TABLE `oer_abo`
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE `latin1_bin` NOT NULL DEFAULT '',
                  MODIFY COLUMN `material_id` CHAR(32) COLLATE `latin1_bin` DEFAULT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_comments`
                  MODIFY COLUMN `comment_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `review_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `foreign_comment_id` CHAR(32) COLLATE `latin1_bin` DEFAULT NULL,
                  MODIFY COLUMN `host_id` CHAR(32) COLLATE `latin1_bin` DEFAULT NULL,
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE `latin1_bin` NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_downloadcounter`
                  MODIFY COLUMN `counter_id` CHAR(32) COLLATE `latin1_bin` NOT NULL DEFAULT '',
                  MODIFY COLUMN `material_id` CHAR(32) COLLATE `latin1_bin` NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_hosts`
                  MODIFY COLUMN `host_id` CHAR(32) COLLATE `latin1_bin` NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_material`
                  MODIFY COLUMN `material_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `foreign_material_id` CHAR(32) COLLATE `latin1_bin` DEFAULT NULL,
                  MODIFY COLUMN `host_id` CHAR(32) COLLATE `latin1_bin` DEFAULT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_material_users`
                  MODIFY COLUMN `material_id` CHAR(32) COLLATE `latin1_bin` NOT NULL DEFAULT '',
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE `latin1_bin` NOT NULL DEFAULT ''";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_reviews`
                  MODIFY COLUMN `review_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `material_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `foreign_review_id` CHAR(32) COLLATE `latin1_bin` DEFAULT NULL,
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `host_id` CHAR(32) COLLATE `latin1_bin` DEFAULT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_tags`
                  MODIFY COLUMN `tag_hash` CHAR(32) COLLATE `latin1_bin` NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_tags_material`
                  MODIFY COLUMN `material_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `tag_hash` CHAR(32) COLLATE `latin1_bin` NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `oer_user`
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `foreign_user_id` CHAR(32) COLLATE `latin1_bin` NOT NULL,
                  MODIFY COLUMN `host_id` CHAR(32) COLLATE `latin1_bin` NOT NULL";
        DBManager::get()->exec($query);
    }
}
