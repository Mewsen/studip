<?php
final class RemoveWikiComments extends Migration
{
    public function description()
    {
        return 'Remove wiki comments configuration setting';
    }

    protected function up()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING(`field`)
                  WHERE `field` = 'WIKI_COMMENTS_ENABLE'";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "INSERT INTO `config` (`field`, `value`, `type`, `range`, `mkdate`, `chdate`, `description`)
                  VALUES (:name, :value, :type, :range, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";
        DBManager::get()->execute($query, [
            ':name'        => 'WIKI_COMMENTS_ENABLE',
            ':description' => 'Einstellung für die Anzeige von Kommentaren in Wiki als Icon',
            ':range'       => 'user',
            ':type'        => 'boolean',
            ':value'       => '0'
        ]);
    }
}
