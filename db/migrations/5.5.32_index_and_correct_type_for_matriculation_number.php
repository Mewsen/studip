<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4577
 */
final class IndexAndCorrectTypeForMatriculationNumber extends Migration
{
    public function description()
    {
        return 'Sets the type of the matriculation number column correctly and adds an index';
    }

    public function up()
    {
        $query = "UPDATE `auth_user_md5`
                  SET `matriculation_number` = NULL
                  WHERE `matriculation_number` = ''";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `auth_user_md5`
                  MODIFY COLUMN `matriculation_number` VARCHAR(255) COLLATE latin1_bin NULL DEFAULT NULL,
                  ADD UNIQUE INDEX `matriculation_number` (`matriculation_number`)";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "ALTER TABLE `auth_user_md5`
                  DROP INDEX `matriculation_number`,
                  MODIFY COLUMN `matriculation_number` VARCHAR(255) NULL DEFAULT NULL";
        DBManager::get()->exec($query);
    }
}
