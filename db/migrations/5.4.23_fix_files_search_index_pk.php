<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6184
 */
final class FixFilesSearchIndexPk extends Migration
{
    public function description()
    {
        return 'Add PK id to files_search_index and remove explicit FTS_DOC_ID if present';
    }

    protected function up()
    {
        $hasFtsDocId = (bool) DBManager::get()->fetchColumn(
            "SHOW COLUMNS FROM `files_search_index` LIKE 'FTS_DOC_ID'"
        );

        if ($hasFtsDocId) {
            DBManager::get()->exec(
                "ALTER TABLE `files_search_index`
                 DROP COLUMN `FTS_DOC_ID`,
                 ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST"
            );
        } else {
            DBManager::get()->exec(
                "ALTER TABLE `files_search_index`
                 ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST"
            );
        }

    }
}
