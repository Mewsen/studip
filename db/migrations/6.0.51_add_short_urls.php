<?php

/**
 * Description of class.
 * Second line
 */
final class AddShortUrls extends Migration
{
    public function description()
    {
        return 'Adds the short_urls table.';
    }

    public function up()
    {
        DBManager::get()->exec(
            "CREATE TABLE short_urls (
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                alias VARCHAR(256) UNIQUE NOT NULL,
                path VARCHAR(256) NOT NULL,
                user_id VARCHAR(32) NOT NULL,
                mkdate INT(11) UNSIGNED NOT NULL,
                chdate INT(11) UNSIGNED NOT NULL
            )"
        );
    }


    public function down()
    {
        DBManager::get()->exec("DROP TABLE short_urls");
    }
}
