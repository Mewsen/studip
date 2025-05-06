<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/5475
 */
final class RepairConsultationEvents extends Migration
{
    use DatabaseMigrationTrait;

    protected function up()
    {
        $block_ids = $this->getBlockIds();

        if (!$block_ids) {
            return;
        }

        $event_table = 'calendar_dates';
        $event_id_column = 'id';

        if (!$this->tableExists($event_table)) {
            $event_table = 'event_data';
            $event_id_column = 'event_id';
        }

        $query = "DELETE `consultation_events`, `{$event_table}`
                  FROM `consultation_events`
                  LEFT JOIN `{$event_table}` ON `{$event_table}`.`{$event_id_column}` = `consultation_events`.`event_id`
                  JOIN `consultation_slots` AS s USING (`slot_id`)
                  WHERE `block_id` IN (?)
                    AND NOT EXISTS (
                      SELECT 1
                      FROM `consultation_responsibilities`
                      WHERE `block_id` = s.`block_id`
                        AND `range_type` = 'user'
                        AND `range_id` = `consultation_events`.`user_id`
                    ) AND NOT EXISTS (
                      SELECT 1
                      FROM `consultation_responsibilities`
                      JOIN `statusgruppe_user` ON `range_id` = `statusgruppe_id`
                      WHERE `block_id` = s.`block_id`
                        AND `range_type` = 'statusgroup'
                        AND `statusgruppe_user`.`user_id` = `consultation_events`.`user_id`
                    ) AND NOT EXISTS (
                      SELECT 1
                      FROM `consultation_responsibilities`
                      JOIN `user_inst` ON `range_id` = `Institut_id`
                      WHERE `block_id` = s.`block_id`
                        AND `range_type` = 'institute'
                        AND `user_inst`.`user_id` = `consultation_events`.`user_id`
                    )";
        DBManager::get()->execute($query, [$block_ids]);
    }

    private function getBlockIds(): array
    {
        // Responsibilities: Users
        $query = "SELECT `block_id`
                  FROM `consultation_responsibilities` AS r
                  JOIN `consultation_slots` AS s USING (`block_id`)
                  JOIN `consultation_events` AS e USING (`slot_id`)
                  WHERE r.`range_type` = 'user'
                  GROUP BY `block_id`
                  HAVING COUNT(DISTINCT r.`range_id`) < COUNT(DISTINCT e.`user_id`)";
        $block_ids0 = DBManager::get()->fetchFirst($query);

        // Responsibilities: Statusgroups
        $query = "SELECT `block_id`
                  FROM `consultation_responsibilities` AS r
                  JOIN `statusgruppe_user` AS su ON r.`range_id` = su.`statusgruppe_id`
                  JOIN `consultation_slots` AS s USING (`block_id`)
                  JOIN `consultation_events` AS e USING (`slot_id`)
                  WHERE r.`range_type` = 'statusgroup'
                  GROUP BY `block_id`
                  HAVING COUNT(DISTINCT su.`user_id`) < COUNT(DISTINCT e.`user_id`)";
        $block_ids1 = DBManager::get()->fetchFirst($query);

        // Responsibilities: Institutes
        $query = "SELECT `block_id`
                  FROM `consultation_responsibilities` AS r
                  JOIN `user_inst` AS ui ON r.`range_id` = ui.`Institut_id`
                  JOIN `consultation_slots` AS s USING (`block_id`)
                  JOIN `consultation_events` AS e USING (`slot_id`)
                  WHERE r.`range_type` = 'statusgroup'
                    AND ui.`inst_perms` IN ('tutor', 'dozent')
                  GROUP BY `block_id`
                  HAVING COUNT(DISTINCT ui.`user_id`) < COUNT(DISTINCT e.`user_id`)";
        $block_ids2 = DBManager::get()->fetchFirst($query);

        return array_unique(array_merge(
            $block_ids0,
            $block_ids1,
            $block_ids2
        ));
    }
}
