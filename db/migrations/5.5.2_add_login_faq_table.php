<?php

class AddLoginFaqTable extends Migration
{
    public function description()
    {
        return 'Create table for login page FAQ';
    }

    public function up()
    {
        $db = DBManager::get();

        $query = "CREATE TABLE IF NOT EXISTS `login_faq`
                (
                `faq_id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                PRIMARY KEY (`faq_id`)
            )";

        $db->exec($query);
    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec('DROP TABLE IF EXISTS `login_faq`');
    }


}
