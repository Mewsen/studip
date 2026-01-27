<?php

class Biest6172RemoveFloatPrecisionFromCp extends Migration
{
    public function description()
    {
        return 'Removes the precision from float.';
    }

    public function up()
    {
        $query = 'ALTER TABLE `mvv_stgteilabschnitt`
                  CHANGE `kp` `kp` FLOAT NULL DEFAULT NULL';
        DBManager::get()->execute($query);
        $query = 'ALTER TABLE `mvv_modul`
                  CHANGE `kp` `kp` FLOAT NULL DEFAULT NULL';
        DBManager::get()->execute($query);
        $query = 'ALTER TABLE `mvv_modulteil`
                  CHANGE `kp` `kp` FLOAT NULL DEFAULT NULL';
        DBManager::get()->execute($query);
    }

    public function down()
    {
        $query = 'ALTER TABLE `mvv_stgteilabschnitt`
                  CHANGE `kp` `kp` DOUBLE(5,2) NULL DEFAULT NULL';
        DBManager::get()->execute($query);
        $query = 'ALTER TABLE `mvv_modul`
                  CHANGE `kp` `kp` DOUBLE(5,2) NULL DEFAULT NULL';
        DBManager::get()->execute($query);
        $query = 'ALTER TABLE `mvv_modulteil`
                  CHANGE `kp` `kp` DOUBLE(5,2) NULL DEFAULT NULL';
        DBManager::get()->execute($query);
    }
}
