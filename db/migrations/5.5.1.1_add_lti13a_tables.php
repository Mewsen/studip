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
                private_key BLOB(16384) NOT NULL DEFAULT '',
                passphrase VARCHAR(512) NOT NULL DEFAULT '',
                mkdate BIGINT(10) NOT NULL DEFAULT '0',
                chdate BIGINT(10) NOT NULL DEFAULT '0'
            )"
        );
        $db->exec("ALTER TABLE `keyrings` ADD INDEX(`range_id`, `range_type`)");

        /*
        $db->exec(
            "CREATE TABLE IF NOT EXISTS lti_registrations (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                client_id BIGINT NOT NULL,
                tool_id CHAR(32) NOT NULL,
                mkdate BIGINT(10) NOT NULL DEFAULT '0',
                chdate BIGINT(10) NOT NULL DEFAULT '0'
            )"
        );
        */
/*
        $db->exec(
            "CREATE TABLE IF NOT EXISTS lti_deployments (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                tool_id CHAR(32) NOT NULL,
                mkdate BIGINT(10) NOT NULL DEFAULT '0',
                chdate BIGINT(10) NOT NULL DEFAULT '0'
            )"
        );
*/

        $db->exec(
            "ALTER TABLE `lti_tool`
            ADD COLUMN lti_version VARCHAR(8) NOT NULL DEFAULT '1.1',
            ADD COLUMN is_global TINYINT(1) NOT NULL DEFAULT '1',
            ADD COLUMN oidc_init_url VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN jwks_url VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN deep_linking_url VARCHAR(255) NOT NULL DEFAULT ''"
        );
    }

    protected function down()
    {
        $db = DBManager::get();
        $db->exec("ALTER TABLE `lti_tool` DROP COLUMN `lti_version`");
        $db->exec('DROP TABLE IF EXISTS lti_deployments');
        $db->exec('DROP TABLE IF EXISTS lti_registrations');
        $db->exec('DROP TABLE IF EXISTS keyrings');
    }
}
