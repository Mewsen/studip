<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6153
 */
final class FixWikiConfigs extends Migration
{
    public function description()
    {
        return 'Converts the wiki configurations WIKI_CREATE_PERMISSION and WIKI_RENAME_PERMISSION to range configurations';
    }

    public function up()
    {
        $query = "UPDATE `config`
                  SET `range` = 'range'
                  WHERE `field` IN('WIKI_CREATE_PERMISSION', 'WIKI_RENAME_PERMISSION')";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "UPDATE `config`
                  SET `range` = 'course'
                  WHERE `field` IN('WIKI_CREATE_PERMISSION', 'WIKI_RENAME_PERMISSION')";
        DBManager::get()->exec($query);
    }
}
