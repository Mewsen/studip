<?php

final class Tic4529 extends Migration
{
    public function description()
    {
        return 'Delete MAIL_USE_SUBJECT_PREFIX and add MAIL_SUBJECT_PREFIX';
    }

    public function up()
    {
        $db = DBManager::get();

        $MAIL_USE_SUBJECT_PREFIX = $db->fetchColumn("SELECT `value` FROM `config_values` WHERE `field` = ?", ["MAIL_USE_SUBJECT_PREFIX"]);
        if (!$MAIL_USE_SUBJECT_PREFIX) {
            $MAIL_USE_SUBJECT_PREFIX = $db->fetchColumn("SELECT `value` FROM `config` WHERE `field` = ?", ["MAIL_USE_SUBJECT_PREFIX"]);
        }

        $value = '';
        if ($MAIL_USE_SUBJECT_PREFIX) {
            $UNI_NAME_CLEAN = $db->fetchColumn("SELECT `value` FROM `config_values` WHERE `field` = ?", ["UNI_NAME_CLEAN"]);

            $value = $UNI_NAME_CLEAN ? sprintf('[Stud.IP - %s]', $UNI_NAME_CLEAN) : '[Stud.IP]';
        }

        $db->execute(
            "INSERT IGNORE INTO `config` VALUES (:field, :value, :type, :range, :section, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :desc)",
            [
                'field' => 'MAIL_SUBJECT_PREFIX',
                'value' => $value,
                'type' => 'string',
                'range' => 'global',
                'section' => 'global',
                'desc' => 'Stellt dem Titel von per Mail versandten Nachrichten'
            ]
        );

        $db->execute(
            "DELETE `config`, `config_values` FROM `config` LEFT JOIN `config_values` USING(`field`) WHERE `field` = ?",
            ["MAIL_USE_SUBJECT_PREFIX"]
        );
    }

    public function down()
    {
        DBManager::get()->execute(
            "DELETE `config`, `config_values` FROM `config` LEFT JOIN `config_values` USING(`field`) WHERE `field` = ?",
            ["MAIL_SUBJECT_PREFIX"]
        );

        DBManager::get()->execute(
            "INSERT IGNORE INTO `config` VALUES (:field, :value, :type, :range, :section, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :desc)",
            [
                'field' => 'MAIL_USE_SUBJECT_PREFIX',
                'value' => '0',
                'type' => 'boolean',
                'range' => 'global',
                'section' => 'global',
                'desc' => 'Stellt dem Titel von per Mail versandten Nachrichten den Wert von UNI_NAME_CLEAN voran.'
            ]
        );
    }
}
