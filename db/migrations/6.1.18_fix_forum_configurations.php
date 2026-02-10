<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6189
 */
final class FixForumConfigurations extends Migration
{
    public function description()
    {
        return 'Fixes new forum configurations to be applied to ranges and not only to courses';
    }

    protected function up()
    {
        $query = "UPDATE `config`
                  SET `range` = 'range'
                  WHERE `field` IN (
                      'FORUM_MODERATION_PERMISSION',
                      'FORUM_HIDE_CATEGORIES_NAVIGATION'
                  )";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "UPDATE `config`
                  SET `range` = 'course'
                  WHERE `field` IN (
                      'FORUM_MODERATION_PERMISSION',
                      'FORUM_HIDE_CATEGORIES_NAVIGATION'
                  )";
        DBManager::get()->exec($query);
    }
}
