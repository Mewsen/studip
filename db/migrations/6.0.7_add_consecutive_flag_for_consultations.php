<?php
return new class extends Migration
{
    public function description()
    {
        return 'Adds new flag "consecutive" to table "consultation_blocks" and stores previous slot information per slot';
    }

    protected function up()
    {
        $query = "ALTER TABLE `consultation_blocks`
                  ADD COLUMN `consecutive` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `lock_time`";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `consultation_slots`
                  ADD COLUMN `previous_slot_id` INT(11) UNSIGNED DEFAULT NULL AFTER `block_id`";
        DBManager::get()->exec($query);

        // THis will set the previous slot relation for all slots
        $query = "UPDATE consultation_slots AS s0
                  JOIN consultation_slots AS s1
                    ON s1.slot_id = (
                      SELECT slot_id
                      FROM consultation_slots AS s2
                      WHERE s2.block_id = s0.block_id
                        AND s2.start_time < s0.start_time
                        AND s2.slot_id != s0.slot_id
                      ORDER BY s2.start_time DESC
                      LIMIT 1
                    )
                  SET s0.previous_slot_id = s1.slot_id";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        $query = "ALTER TABLE `consultation_slots`
                  DROP COLUMN `previous_slot_id`";
        DBManager::get()->exec($query);

        $query = "ALTER TABLE `consultation_blocks`
                  DROP COLUMN `consecutive`";
        DBManager::get()->exec($query);
    }
};
