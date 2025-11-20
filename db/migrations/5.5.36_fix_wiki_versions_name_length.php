<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6048
 */
final class FixWikiVersionsNameLength extends Migration
{
    public function description()
    {
        return 'Changes the length of column wiki_versions.name to match wiki_pages.name';
    }

    protected function up()
    {
        $query = "ALTER TABLE `wiki_versions`
                  MODIFY COLUMN `name` VARCHAR(255) NOT NULL";
        DBManager::get()->exec($query);
    }
}
