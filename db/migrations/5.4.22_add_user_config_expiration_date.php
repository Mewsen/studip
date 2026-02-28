<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/6140
 */
final class AddUserConfigExpirationDate extends Migration
{
    public function description()
    {
        return 'Creates the user config entry for the expiration date';
    }

    public function up()
    {
        $query = "INSERT INTO `config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
                  VALUES ('EXPIRATION_DATE', '', 'integer', 'user', 'global', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'Expiration date of the user account')";
        DBManager::get()->exec($query);
    }
}
