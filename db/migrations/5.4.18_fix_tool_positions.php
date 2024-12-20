<?php

class FixToolPositions extends Migration
{
    public function description()
    {
        return 'move admin tool to first position in course';
    }

    public function up()
    {
        $db = DBManager::get();

        // update default order in sem_classes
        $result = $db->query('SELECT * FROM sem_classes');
        $update = $db->prepare('UPDATE sem_classes SET modules = ? WHERE id = ?');

        foreach ($result as $row) {
            $modules = json_decode($row['modules'], true);
            $admin = $modules['CoreAdmin'] ?? $modules['CoreStudygroupAdmin'];
            unset($modules['CoreAdmin'], $modules['CoreStudygroupAdmin']);

            if ($row['studygroup_mode']) {
                $modules = ['CoreStudygroupAdmin' => $admin] + $modules;
            } else {
                $modules = ['CoreAdmin' => $admin] + $modules;
            }

            $update->execute([json_encode($modules), $row['id']]);
        }

        // update individual order in tools_activated
        $stmt = $db->query('SELECT pluginclassname, pluginid FROM plugins');
        $plugins = $stmt->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
        $admin_ids = [$plugins['CoreAdmin'], $plugins['CoreStudygroupAdmin']];

        $result = $db->query('SELECT * FROM tools_activated ORDER BY range_id, position');
        $update = $db->prepare('UPDATE tools_activated SET position = ? WHERE range_id = ? AND plugin_id = ?');
        $range_id = null;

        foreach ($result as $row) {
            if ($range_id !== $row['range_id']) {
                $range_id = $row['range_id'];
                $position = 0;
            }

            if (in_array($row['plugin_id'], $admin_ids)) {
                $new_position = 0;
            } else {
                $new_position = ++$position;
            }

            if ($row['position'] != $new_position) {
                $update->execute([$new_position, $range_id, $row['plugin_id']]);
            }
        }
    }
}
