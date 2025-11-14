<?php

final class RenameBlubberMentionsTable extends Migration
{
    private function rename_table_full($old_tbl_name, $new_tbl_name, $old_pr_key, $new_pr_key)
    {
        // Rename pr key.
        $query_rename_primary_key = "ALTER TABLE `{$old_tbl_name}`
                    CHANGE `{$old_pr_key}` `{$new_pr_key}` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT";
        DBManager::get()->exec($query_rename_primary_key);

        // Change pr key.
        $query_primary_key_replacement = "ALTER TABLE `{$old_tbl_name}`
                    DROP PRIMARY KEY,
                    ADD PRIMARY KEY (`{$new_pr_key}`)";
        DBManager::get()->exec($query_primary_key_replacement);

        // Rename table.
        $query_rename_table = "RENAME TABLE `{$old_tbl_name}` TO `{$new_tbl_name}`";
        DBManager::get()->exec($query_rename_table);
    }

    protected function up()
    {
        $old_tbl_name = 'blubber_mentions';
        $new_tbl_name = 'blubber_participations';
        $old_pr_key = 'mention_id';
        $new_pr_key = 'participation_id';
        $this->rename_table_full($old_tbl_name, $new_tbl_name, $old_pr_key, $new_pr_key);
    }

    protected function down()
    {
        $old_tbl_name = 'blubber_participations';
        $new_tbl_name = 'blubber_mentions';
        $old_pr_key = 'participation_id';
        $new_pr_key = 'mention_id';
        $this->rename_table_full($old_tbl_name, $new_tbl_name, $old_pr_key, $new_pr_key);
    }
}
