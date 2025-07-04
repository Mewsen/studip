<?php
final class Forum3CorrectColumns extends Migration
{
    public function description()
    {
        return 'Corrects columns in forum3';
    }

    public function up()
    {
        $query = "ALTER TABLE `forum_categories`
                  MODIFY COLUMN `chdate` INT(11) UNSIGNED DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED DEFAULT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_discussions`
                  MODIFY COLUMN `sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                  MODIFY COLUMN `closed_at` INT(11) UNSIGNED DEFAULT NULL,
                  MODIFY COLUMN `chdate` INT(11) UNSIGNED NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_posting_reactions`
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_posting_reads`
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `read_index` INT(11) UNSIGNED DEFAULT 0,
                  MODIFY COLUMN `chdate` INT(11) UNSIGNED NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_postings`
                  MODIFY COLUMN `user_id` CHAR(32) COLLATE latin1_bin NOT NULL,
                  MODIFY COLUMN `anonymous` TINYINT(1) UNSIGNED DEFAULT 0,
                  MODIFY COLUMN `chdate` INT(11) UNSIGNED NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_topics`
                  MODIFY COLUMN `position` INT(11) UNSIGNED DEFAULT 0,
                  MODIFY COLUMN `chdate` INT(11) UNSIGNED NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) UNSIGNED NOT NULL";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "ALTER TABLE `forum_topics`
                  MODIFY COLUMN `position` INT(11) DEFAULT 0,
                  MODIFY COLUMN `chdate` INT(11) NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_postings`
                  MODIFY COLUMN `user_id` CHAR(32) NOT NULL,
                  MODIFY COLUMN `anonymous` TINYINT(1) DEFAULT 0,
                  MODIFY COLUMN `chdate` INT(11) NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_posting_reads`
                  MODIFY COLUMN `user_id` CHAR(32) NOT NULL,
                  MODIFY COLUMN `read_index` INT(11) DEFAULT 0,
                  MODIFY COLUMN `chdate` INT(11) NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_posting_reactions`
                  MODIFY COLUMN `mkdate` INT(11) NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_discussions`
                  MODIFY COLUMN `sticky` TINYINT(1) NOT NULL DEFAULT 0,
                  MODIFY COLUMN `closed_at` INT(11) DEFAULT NULL,
                  MODIFY COLUMN `chdate` INT(11) NOT NULL,
                  MODIFY COLUMN `mkdate` INT(11) NOT NULL";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `forum_categories`
                  MODIFY COLUMN `chdate` INT(11) DEFAULT NULL,
                  MODIFY COLUMN `mkdate` INT(11) DEFAULT NULL";
        DBManager::get()->exec($query);
    }
}
