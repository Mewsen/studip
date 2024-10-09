<?php

final class UpdateHelpContent extends Migration
{
    public function description()
    {
        return 'Update route for avatar help content';
    }
    protected function up()
    {
        DBManager::get()->exec(
            "UPDATE `help_content` SET `route` = 'dispatch.php/course/avatar' WHERE `help_content`.`content_id` = 'abfb5d03de288d02df436f9a8bb96d9d'"
        );
        DBManager::get()->exec(
            "UPDATE `help_content` SET `route` = 'dispatch.php/course/avatar' WHERE `help_content`.`content_id` = '5fab81bbd1e19949f304df08ea21ca1b'"
        );

        
    }

    protected function down()
    {
        DBManager::get()->exec(
            "UPDATE `help_content` SET `route` = 'dispatch.php/course/avatar/update' WHERE `help_content`.`content_id` = 'abfb5d03de288d02df436f9a8bb96d9d'"
        );
        DBManager::get()->exec(
            "UPDATE `help_content` SET `route` = 'dispatch.php/course/avatar/update' WHERE `help_content`.`content_id` = '5fab81bbd1e19949f304df08ea21ca1b'"
        );
    }
}
