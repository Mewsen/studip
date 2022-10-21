<?php


class AddShortUrlsTable extends Migration
{
    public function description()
    {
        return 'Adds the short_urls table.';
    }


    public function up()
    {
        $db = DBManager::get();
        $db->exec(
            "CREATE TABLE short_urls (
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                alias VARCHAR(256) UNIQUE NOT NULL DEFAULT '',
                url VARCHAR(1024) NOT NULL,
                user_id VARCHAR(32) NOT NULL DEFAULT '',
                mkdate int(10) NOT NULL DEFAULT '0',
                chdate int(10) NOT NULL DEFAULT '0'
            )"
        );
    }


    public function down()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE short_urls");
    }
}
