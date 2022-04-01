<?php
final class AddIndexToCwUserProgressesV50 extends Migration
{
    public function description()
    {
        return 'Alter cw_user_progresses table, add index for block_id';
    }

    public function up()
    {
        // avoid running this migration twice
        $query = "SHOW INDEX FROM `cw_user_progresses` WHERE Key_name = 'block_id'";
        $result = DBManager::get()->query($query);

        if ($result && $result->rowCount() > 0) {
            return;
        }

        $query = "ALTER TABLE `cw_user_progresses`
                  ADD INDEX `block_id` (`block_id`)";
        DBManager::get()->exec($query);
    }

    public function down()
    {
        $query = "ALTER TABLE `cw_user_progresses`
                  DROP INDEX `block_id`";
        DBManager::get()->exec($query);
    }
}
