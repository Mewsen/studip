<?php
final class Step5383NewDataFieldTypesForMvv extends Migration
{
    public function description()
    {
        return 'Adds new data field types related to StgteilAbschnitModul and ModulteilStgteilAbschnitt objects';
    }

    public function up()
    {
        $query = "ALTER TABLE `datafields`
                    CHANGE `object_type` `object_type` ENUM('sem','inst','user',
                        'userinstrole','usersemdata','roleinstdata','moduldeskriptor',
                        'modulteildeskriptor','studycourse',
                        'stgteilabschnittmodul','modulteilstgteilabschnitt')
                        NULL DEFAULT NULL";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "ALTER TABLE `datafields`
                    CHANGE `object_type` `object_type` ENUM('sem','inst','user',
                        'userinstrole','usersemdata','roleinstdata','moduldeskriptor',
                        'modulteildeskriptor','studycourse')
                        NULL DEFAULT NULL";
        DBManager::get()->exec($query);
    }
}
