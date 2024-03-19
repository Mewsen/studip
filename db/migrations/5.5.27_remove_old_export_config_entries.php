<?php

final class RemoveOldExportConfigEntries extends Migration
{
    public function description()
    {
        return 'Removes remnants of the old export';
    }

    public function up()
    {
        DBManager::get()->execute("DELETE FROM `config` WHERE `field` = 'XSLT_ENABLE'");
        DBManager::get()->execute("DELETE FROM `config` WHERE `field` = 'FOP_ENABLE'");
    }

    public function down()
    {
        DBManager::get()->execute("INSERT INTO `config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
            VALUES('XSLT_ENABLE', '1', 'boolean', 'global', 'global', 1510849314, 1510849314, 'Soll Export mit XSLT angeschaltet sein?')"
        );
        DBManager::get()->execute("INSERT INTO `config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
            VALUES('FOP_ENABLE', '1', 'boolean', 'global', 'global', 1510849314, 1510849314, 'Soll Export mit FOP erlaubt sein?')"
        );
    }
}
