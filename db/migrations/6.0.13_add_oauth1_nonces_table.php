<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4463
 */
return new class extends Migration
{
    public function description()
    {
        return 'Adds a database that stores the already used oauth1 nonces';
    }

    protected function up()
    {
        $query = "CREATE TABLE `oauth1_nonces` (
                     `timestamp` TIMESTAMP NOT NULL,
                     `nonce` VARCHAR(128) NOT NULL,
                     PRIMARY KEY (`timestamp`, `nonce`)
                  )";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "DROP TABLE `oauth1_nonces`";
        DBManager::get()->exec($query);
    }
};
