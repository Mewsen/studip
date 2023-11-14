<?php


class AddTooltipFieldsForLogin extends Migration
{
    public function description()
    {
        return 'Creates config for login username and password tooltip texts';
    }

    public function up()
    {
        $db = DBManager::get();

        $query = 'INSERT INTO `config` (`field`, `value`, `type`, `section`, `range`, `description`, `mkdate`, `chdate`)
                  VALUES (:name, :value, :type, :section, :range, :description, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            'name'          => 'USERNAME_TOOLTIP_TEXT',
            'value'         => 'Geben Sie hier Ihren Stud.IP-Benutzernamen ein.',
            'type'          => 'string',
            'section'       => 'Loginseite',
            'range'         => 'global',
            'description'   => 'Text für den Tooltip des Benutzernamens auf der Loginseite'
        ]);

        $statement->execute([
            'name'          => 'PASSWORD_TOOLTIP_TEXT',
            'value'         => 'Geben Sie hier Ihr Stud.IP-Passwort ein. Achten Sie bei der Eingabe auf Groß- und Kleinschreibung.',
            'type'          => 'string',
            'section'       => 'Loginseite',
            'range'         => 'global',
            'description'   => 'Text für den Tooltip des Benutzernamens auf der Loginseite'
        ]);

        $statement->execute([
            'name'          => 'USERNAME_TOOLTIP_ACTIVATED',
            'value'         => '1',
            'type'          => 'boolean',
            'section'       => 'Loginseite',
            'range'         => 'global',
            'description'   => 'Soll der Tooltip beim Benutzernamen auf der Loginseite sichtbar sein?'
        ]);

        $statement->execute([
            'name'          => 'PASSWORD_TOOLTIP_ACTIVATED',
            'value'         => '1',
            'type'          => 'boolean',
            'section'       => 'Loginseite',
            'range'         => 'global',
            'description'   => 'Soll der Tooltip beim Passwort auf der Loginseite sichtbar sein?'
        ]);

    }

    public function down()
    {
        $db = DBManager::get();

        $query = "DELETE `config`, `config_values`
                  FROM `config` LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'USERNAME_TOOLTIP_TEXT'";
        DBManager::get()->exec($query);

        $query = "DELETE `config`, `config_values`
                  FROM `config` LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'PASSWORD_TOOLTIP_TEXT'";
        DBManager::get()->exec($query);

        $query = "DELETE `config`, `config_values`
                  FROM `config` LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'USERNAME_TOOLTIP_ACTIVATED'";
        DBManager::get()->exec($query);

        $query = "DELETE `config`, `config_values`
                  FROM `config` LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'PASSWORD_TOOLTIP_ACTIVATED'";
        DBManager::get()->exec($query);
    }
}
