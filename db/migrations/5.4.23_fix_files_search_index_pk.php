<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6184
 */
final class FixFilesSearchIndexPk extends Migration
{
    use DatabaseMigrationTrait;

    public function description()
    {
        return 'Add PK id to files_search_index and remove explicit FTS_DOC_ID if present';
    }

    protected function up()
    {
        if ($this->columnExists('files_search_index', 'FTS_DOC_ID')) {
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
