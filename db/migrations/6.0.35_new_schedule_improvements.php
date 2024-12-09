<?php


class NewScheduleImprovements extends Migration
{
    use DatabaseMigrationTrait;

    public function description()
    {
        return 'A bugfix migration to add colours to personal schedule entries again and to migrate schedule configurations.';
    }

    protected function up()
    {
        $db = DBManager::get();
        if (!$this->columnExists('schedule_entries', 'colour_id')) {
            $db->exec(
                "ALTER TABLE `schedule_entries`
                ADD COLUMN `colour_id` TINYINT(3) NOT NULL DEFAULT 0"
            );
        }

        //Migrate the content of schedule configuration entries:

        $fetch_stmt = $db->prepare(
            "SELECT `range_id`, `value`
             FROM `config_values`
             WHERE `field` = 'SCHEDULE_SETTINGS'"
        );
        $update_stmt = $db->prepare(
            "UPDATE `config_values`
            SET `value` = :new_value, `chdate` = UNIX_TIMESTAMP()
            WHERE `field` = 'SCHEDULE_SETTINGS'
            AND `range_id` = :range_id"
        );
        $delete_stmt = $db->prepare(
            "DELETE FROM `config_values`
            WHERE `field` = 'SCHEDULE_SETTINGS'
            AND `range_id` = :range_id"
        );

        $fetch_stmt->execute();
        while ($row = $fetch_stmt->fetch(PDO::FETCH_ASSOC)) {
            $old_config = json_decode($row['value'], true);
            if (is_array($old_config)) {
                //Convert the configuration:
                $new_config = [
                    'start_time' => sprintf('%02u:00', $old_config['glb_start_time']),
                    'end_time'   => sprintf('%02u:00', $old_config['glb_end_time']),
                    'semester_id' => $old_config['semester_id'] ?? $old_config['glb_sem'] ?? null,
                ];
                if (count($old_config['glb_days']) === 7) {
                    $new_config['weekdays'] = 7;
                } else {
                    $new_config['weekdays'] = 5;
                }
                //Convert the visible days array:
                $visible_days = [];
                if (is_array($old_config['glb_days'])) {
                    foreach ($old_config['glb_days'] as $day) {
                        if ($day == 0) {
                            $visible_days[] = 7;
                        } else {
                            $visible_days[] = (int) $day;
                        }
                    }
                }
                $new_config['visible_days'] = $visible_days;
                $update_stmt->execute([
                    'range_id'  => $row['range_id'],
                    'new_value' => json_encode($new_config)
                ]);
            } else {
                //Delete the configuration:
                $delete_stmt->execute(['range_id' => $old_config['range_id']]);
            }
        }
    }
}
