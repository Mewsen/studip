<?php


class AddLti13aTables extends Migration
{
    public function description()
    {
        return 'Add tables for the LTI 1.3A functionality.';
    }

    protected function up()
    {
        $db = DBManager::get();

        $db->exec(
            "CREATE TABLE IF NOT EXISTS keyrings (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                range_id CHAR(32) NOT NULL,
                range_type VARCHAR(16) NOT NULL,
                public_key BLOB(16384) NOT NULL,
                private_key BLOB(16384) NOT NULL,
                passphrase VARCHAR(512) NOT NULL DEFAULT '',
                mkdate BIGINT(10) NOT NULL DEFAULT '0',
                chdate BIGINT(10) NOT NULL DEFAULT '0'
            )"
        );
        $db->exec("ALTER TABLE `keyrings` ADD INDEX(`range_id`, `range_type`)");
    }

    protected function down()
    {
        $db = DBManager::get();
        $db->exec('DROP TABLE IF EXISTS keyrings');
    }
}
