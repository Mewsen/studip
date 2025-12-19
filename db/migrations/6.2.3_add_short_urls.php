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
            "CREATE TABLE IF NOT EXISTS short_urls (
                id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                alias VARCHAR(255) UNIQUE NOT NULL,
                path VARCHAR(255) NOT NULL,
                title VARCHAR(255) NOT NULL,
                user_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
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
