<?php


class AddLoginFaqConfig extends Migration
{
    public function description()
    {
        return 'Creates configs for login faq: Visibility and title (eg.: Hilfe zum Login)';
    }

    public function up()
    {
        $db = DBManager::get();

        $query = 'INSERT INTO `config` (`field`, `value`, `type`, `section`, `range`, `description`, `mkdate`, `chdate`)
                  VALUES (:name, :value, :type, :section, :range, :description, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            'name'          => 'LOGIN_FAQ_TITLE',
            'value'         => 'Hinweise zum Login',
            'type'          => 'string',
            'section'       => 'Loginseite',
            'range'         => 'global',
            'description'   => 'Überschrift für den FAQ-Bereich auf der Loginseite'
        ]);

        $statement->execute([
            'name'          => 'LOGIN_FAQ_VISIBILITY',
            'value'         => '1',
            'type'          => 'boolean',
            'section'       => 'Loginseite',
            'range'         => 'global',
            'description'   => 'Soll der FAQ-Bereich auf der Loginseite sichtbar sein?'
        ]);

    }

    public function down()
    {
        $db = DBManager::get();

        $query = "DELETE `config`, `config_values`
                  FROM `config` LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'LOGIN_FAQ_TITLE'";
        DBManager::get()->exec($query);

        $query = "DELETE `config`, `config_values`
                  FROM `config` LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'LOGIN_FAQ_VISIBILITY'";
        DBManager::get()->exec($query);
    }
}
