<?php
final class RemoveWikiCourseEditRestrictionConfiguration extends Migration
{
    public function description()
    {
        return 'Removes configuration "WIKI_COURSE_EDIT_RESTRICTED"';
    }

    public function up()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'WIKI_COURSE_EDIT_RESTRICTED'";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "INSERT INTO `config` (`field`, `value`, `type`, `range`, `mkdate`, `chdate`, `description`)
                  VALUES (:name, :value, :type, :range, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";
        DBManager::get()->execute($query, [
            'name'        => 'WIKI_COURSE_EDIT_RESTRICTED',
            'description' => 'Legt fest, dass nur Teilnehmende ab Rechtestufe "tutor" das Wiki bearbeiten dürfen.',
            'range'       => 'course',
            'type'        => 'boolean',
            'value'       => '0'
        ]);
    }
}
