<?php


class AddBannerMobilePath extends Migration
{
    public function description()
    {
        return 'Add field banner_mobile_path to table banner_ads';
    }


    public function up()
    {
        $db = DBManager::get();

        $db->exec(
            "ALTER TABLE `banner_ads`
            ADD `banner_mobile_path` varchar(255) COLLATE utf8mb4_unicode_ci NULL AFTER `banner_path`"
        );
    }


    public function down()
    {
        $db = DBManager::get();

        $db->exec(
            "ALTER TABLE `banner_ads` DROP `banner_mobile_path`"
        );

    }
}
