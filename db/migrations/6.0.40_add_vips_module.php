<?php

class AddVipsModule extends Migration
{
    public function description()
    {
        return 'initial database setup for Vips';
    }

    public function up()
    {
        $db = DBManager::get();

        // install as core plugin
        $sql = "INSERT INTO plugins (pluginclassname, pluginname, plugintype, enabled, navigationpos)
                VALUES ('VipsModule', 'Aufgaben', 'StudipModule,SystemPlugin,PrivacyPlugin,Courseware\\\\CoursewarePlugin', 'yes', 1)";
        $db->exec($sql);
        $id = $db->lastInsertId();

        $sql = "INSERT INTO roles_plugins (roleid, pluginid)
                SELECT roleid, ? FROM roles WHERE `system` = 'y'";
        $db->execute($sql, [$id]);

        // copy tool activations from Vips plugin
        $sql = "INSERT INTO tools_activated
                SELECT range_id, range_type, ?, position, metadata, mkdate, chdate FROM tools_activated
                WHERE plugin_id = (SELECT pluginid FROM plugins WHERE pluginname = 'Vips')";
        $db->execute($sql, [$id]);

        // update etask tables
        $sql = "ALTER TABLE etask_assignments
                CHANGE type type varchar(64) COLLATE latin1_bin NOT NULL,
                CHANGE active active tinyint UNSIGNED NOT NULL DEFAULT 1,
                ADD weight float NOT NULL DEFAULT 0 AFTER active,
                ADD block_id int DEFAULT NULL AFTER weight,
                ADD KEY test_id (test_id),
                ADD KEY range_id (range_id)";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_assignment_attempts
                ADD ip_address varchar(39) COLLATE latin1_bin NOT NULL AFTER end,
                CHANGE options options text DEFAULT NULL,
                ADD UNIQUE KEY assignment_id (assignment_id,user_id)";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_responses
                CHANGE response response mediumtext NOT NULL,
                ADD student_comment text DEFAULT NULL AFTER response,
                ADD ip_address varchar(39) COLLATE latin1_bin NOT NULL AFTER student_comment,
                ADD commented_solution text DEFAULT NULL AFTER feedback,
                ADD KEY assignment_id (assignment_id,task_id,user_id),
                ADD KEY user_id (user_id),
                ADD KEY task_id (task_id)";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_tasks
                CHANGE type type varchar(64) COLLATE latin1_bin NOT NULL,
                CHANGE description description mediumtext NOT NULL,
                CHANGE task task mediumtext NOT NULL,
                ADD KEY user_id (user_id)";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_tests
                CHANGE description description mediumtext NOT NULL,
                CHANGE options options text DEFAULT NULL,
                ADD KEY user_id (user_id)";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_test_tasks
                ADD part int NOT NULL DEFAULT 0 AFTER position,
                ADD KEY task_id (task_id)";
        $db->exec($sql);

        // add new tables
        $sql = "CREATE TABLE etask_blocks (
                  id int NOT NULL AUTO_INCREMENT,
                  name varchar(255) NOT NULL,
                  range_id char(32) COLLATE latin1_bin NOT NULL,
                  group_id char(32) COLLATE latin1_bin DEFAULT NULL,
                  visible tinyint NOT NULL DEFAULT 1,
                  weight float DEFAULT NULL,
                  PRIMARY KEY (id),
                  KEY range_id (range_id)
                )";
        $db->exec($sql);

        $sql = "CREATE TABLE etask_group_members (
                  group_id char(32) COLLATE latin1_bin NOT NULL,
                  user_id char(32) COLLATE latin1_bin NOT NULL,
                  start int unsigned NOT NULL,
                  end int unsigned DEFAULT NULL,
                  PRIMARY KEY (group_id,user_id,start),
                  KEY user_id (user_id)
                )";
        $db->exec($sql);

        // add settings (unless already present)
        $sql = 'INSERT IGNORE INTO `config` (`field`, `value`, `type`, `range`, `mkdate`, `chdate`, `description`)
                VALUES (:name, :value, :type, :range, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)';
        $statement = DBManager::get()->prepare($sql);
        $statement->execute([
            ':name'        => 'VIPS_COURSE_GRADES',
            ':description' => 'Kursbezogenes Schema zur Notenverteilung in Vips',
            ':range'       => 'course',
            ':type'        => 'array',
            ':value'       => '[]'
        ]);
        $statement->execute([
            ':name'        => 'VIPS_EXAM_RESTRICTIONS',
            ':description' => 'Sperrt während einer Klausur andere Bereiche von Stud.IP für die Teilnehmenden',
            ':range'       => 'global',
            ':type'        => 'boolean',
            ':value'       => '0'
        ]);
        $statement->execute([
            ':name'        => 'VIPS_EXAM_ROOMS',
            ':description' => 'Zentral verwaltete IP-Adressen für PC-Räume',
            ':range'       => 'global',
            ':type'        => 'array',
            ':value'       => '[]'
        ]);
        $statement->execute([
            ':name'        => 'VIPS_EXAM_TERMS',
            ':description' => 'Teilnahmebedingungen, die vor Beginn einer Klausur zu akzeptieren sind',
            ':range'       => 'global',
            ':type'        => 'string',
            ':value'       => ''
        ]);

        // copy data from Vips plugin
        $result = $db->query("SHOW TABLES LIKE 'vips_assignment'");

        if ($result->rowCount() > 0) {
            $this->copyVipsData();
        }
    }

    private function copyVipsData()
    {
        $db = DBManager::get();
        $now = time();

        $task_id = [];
        $test_id = [];
        $assignment_id = [];
        $response_id = [];
        $group_id = [];
        $folder_id = [];

        $task_mapping = [
            'sc_exercise'    => 'SingleChoiceTask',
            'mc_exercise'    => 'MultipleChoiceTask',
            'mco_exercise'   => 'MatrixChoiceTask',
            'lt_exercise'    => 'TextLineTask',
            'tb_exercise'    => 'TextTask',
            'cloze_exercise' => 'ClozeTask',
            'rh_exercise'    => 'MatchingTask',
            'seq_exercise'   => 'SequenceTask'
        ];

        // etask_tasks
        $sql = 'INSERT INTO etask_tasks (type, title, description, task, user_id, mkdate, chdate, options)
                VALUES (:type, :title, :description, :task, :user_id, :mkdate, :chdate, :options)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_exercise');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            $values = [
                'type'        => $task_mapping[$row['type']] ?? $row['type'],
                'title'       => $row['title'],
                'description' => $row['description'],
                'task'        => $row['task_json'],
                'user_id'     => $row['user_id'],
                'mkdate'      => strtotime($row['created']),
                'chdate'      => $now,
                'options'     => $row['options'] ?: '[]'
            ];
            $stmt->execute($values);
            $task_id[$row['id']] = $db->lastInsertId();
        }

        // etask_tests
        $sql = 'INSERT INTO etask_tests (title, description, user_id, mkdate, chdate, options)
                VALUES (:title, :description, :user_id, :mkdate, :chdate, :options)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_test');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            $values = [
                'title'       => $row['title'],
                'description' => $row['description'],
                'user_id'     => $row['user_id'],
                'mkdate'      => strtotime($row['created']),
                'chdate'      => $now,
                'options'     => null
            ];
            $stmt->execute($values);
            $test_id[$row['id']] = $db->lastInsertId();
        }

        // etask_test_tasks
        $sql = 'INSERT INTO etask_test_tasks (test_id, task_id, position, part, points, options, mkdate, chdate)
                VALUES (:test_id, :task_id, :position, :part, :points, :options, :mkdate, :chdate)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_exercise_ref');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            if (isset($test_id[$row['test_id']]) && isset($task_id[$row['exercise_id']])) {
                $values = [
                    'test_id'     => $test_id[$row['test_id']],
                    'task_id'     => $task_id[$row['exercise_id']],
                    'position'    => $row['position'],
                    'part'        => $row['part'],
                    'points'      => $row['points'],
                    'mkdate'      => $now,
                    'chdate'      => $now,
                    'options'     => '',
                ];
                $stmt->execute($values);
            }
        }

        // etask_assignments
        $sql = 'INSERT INTO etask_assignments (test_id, range_type, range_id, type, start, end, active, weight, block_id, options, mkdate, chdate)
                VALUES (:test_id, :range_type, :range_id, :type, :start, :end, :active, :weight, :block_id, :options, :mkdate, :chdate)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_assignment');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            if (isset($test_id[$row['test_id']])) {
                $options = json_decode($row['options'], true);
                unset($options['shuffle_answers']);
                unset($options['printable']);

                $values = [
                    'test_id'     => $test_id[$row['test_id']],
                    'range_type'  => $row['context'],
                    'range_id'    => $row['course_id'],
                    'type'        => $row['type'],
                    'start'       => strtotime($row['start']),
                    'end'         => strtotime($row['end']),
                    'active'      => $row['active'],
                    'weight'      => $row['weight'],
                    'block_id'    => $row['block_id'],
                    'options'     => json_encode($options),
                    'mkdate'      => $now,
                    'chdate'      => $now
                ];
                $stmt->execute($values);
                $assignment_id[$row['id']] = $db->lastInsertId();
            }
        }

        // etask_assignment_attempts
        $sql = 'INSERT INTO etask_assignment_attempts (assignment_id, user_id, start, end, ip_address, options, mkdate, chdate)
                VALUES (:assignment_id, :user_id, :start, :end, :ip_address, :options, :mkdate, :chdate)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_assignment_attempt');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            if (isset($assignment_id[$row['assignment_id']])) {
                $values = [
                    'assignment_id' => $assignment_id[$row['assignment_id']],
                    'user_id'       => $row['user_id'],
                    'start'         => strtotime($row['start']),
                    'end'           => $row['end'] ? strtotime($row['end']) : null,
                    'ip_address'    => $row['ip_address'],
                    'options'       => $row['options'],
                    'mkdate'        => $now,
                    'chdate'        => $now
                ];
                $stmt->execute($values);
            }
        }

        // etask_responses
        $sql = 'INSERT INTO etask_responses (assignment_id, task_id, user_id, response, student_comment, ip_address, state, points, feedback, commented_solution, grader_id, mkdate, chdate, options)
                SELECT :assignment_id, :task_id, user_id, response, student_comment, ip_address, corrected, points, corrector_comment, commented_solution, corrector_id, UNIX_TIMESTAMP(time), UNIX_TIMESTAMP(correction_time), options
                FROM :table WHERE id = :id';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT id, exercise_id, assignment_id, 0 as archive FROM vips_solution UNION SELECT id, exercise_id, assignment_id, 1 as archive FROM vips_solution_archive ORDER BY id');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            if (isset($assignment_id[$row['assignment_id']]) && isset($task_id[$row['exercise_id']])) {
                $stmt->bindValue(':assignment_id', $assignment_id[$row['assignment_id']]);
                $stmt->bindValue(':task_id', $task_id[$row['exercise_id']]);
                $stmt->bindValue(':table', $row['archive'] ? 'vips_solution_archive' : 'vips_solution', StudipPDO::PARAM_COLUMN);
                $stmt->bindValue(':id', $row['id']);
                $stmt->execute();
                $response_id[$row['id']] = $db->lastInsertId();
            }
        }

        // statusgruppen
        $sql = 'INSERT INTO statusgruppen (statusgruppe_id, name, range_id, position, size, mkdate, chdate)
                VALUES (:statusgruppe_id, :name, :range_id, :position, :size, :mkdate, :chdate)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_group');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            $id = md5($row['id'] . ':' . uniqid('statusgruppen', true));
            $position = $db->fetchColumn('SELECT MAX(position) FROM statusgruppen WHERE range_id = ?', [$row['course_id']]);

            $values = [
                'statusgruppe_id' => $id,
                'name'            => $row['name'],
                'range_id'        => $row['course_id'],
                'position'        => $position + 1,
                'size'            => $row['size'],
                'mkdate'          => $now,
                'chdate'          => $now
            ];
            $stmt->execute($values);
            $group_id[$row['id']] = $id;
        }

        // etask_blocks
        $sql = 'INSERT INTO etask_blocks (id, name, range_id, group_id, visible, weight)
                SELECT id, name, course_id, group_id, visible, weight FROM vips_block';
        $db->exec($sql);

        // etask_group_members
        $sql = 'INSERT INTO etask_group_members (group_id, user_id, start, end)
                VALUES (:group_id, :user_id, :start, :end)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_group_member');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            if (isset($group_id[$row['group_id']])) {
                $values = [
                    'group_id'   => $group_id[$row['group_id']],
                    'user_id'    => $row['user_id'],
                    'start'      => strtotime($row['start']),
                    'end'        => strtotime($row['end'])
                ];
                $stmt->execute($values);
            }
        }

        // files
        $sql = 'INSERT INTO files (id, user_id, mime_type, name, size, mkdate, chdate)
                VALUES (:id, :user_id, :mime_type, :name, :size, :mkdate, :chdate)';
        $stmt = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_file');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            $values = [
                'id'          => $row['id'],
                'user_id'     => $row['user_id'],
                'mime_type'   => $row['mime_type'],
                'name'        => $row['name'],
                'size'        => $row['size'],
                'mkdate'      => strtotime($row['created']),
                'chdate'      => $now
            ];
            $stmt->execute($values);
        }

        // folders and file_refs
        $sql = 'INSERT INTO folders (id, user_id, parent_id, range_id, range_type, folder_type, name, data_content, description, mkdate, chdate)
                VALUES (:id, :user_id, :parent_id, :range_id, :range_type, :folder_type, :name, :data_content, :description, :mkdate, :chdate)';
        $stmt_folder = $db->prepare($sql);
        $sql = "INSERT INTO file_refs (id, file_id, folder_id, description, content_terms_of_use_id, user_id, name, mkdate, chdate)
                VALUES (:id, :file_id, :folder_id, :description, 'UNDEF_LICENSE', :user_id, :name, :mkdate, :chdate)";
        $stmt_file_ref = $db->prepare($sql);
        $data = $db->query('SELECT * FROM vips_file_ref JOIN vips_file ON vips_file_ref.file_id = vips_file.id');

        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
            if ($row['type'] === 'exercise') {
                $range_id = $task_id[$row['object_id']] ?? null;
                $range_type = 'task';
                $folder_type = 'ExerciseFolder';
            } else {
                $range_id = $response_id[$row['object_id']] ?? null;
                $range_type = 'response';
                $folder_type = $row['type'] === 'solution' ? 'ResponseFolder' : 'FeedbackFolder';
            }

            if (isset($range_id)) {
                if (!isset($folder_id[$row['object_id'] . ':' . $row['type']])) {
                    $new_folder_id = md5($row['object_id'] . ':' . uniqid('folders', true));
                    $values = [
                        'id'           => $new_folder_id,
                        'user_id'      => $row['user_id'],
                        'parent_id'    => '',
                        'range_id'     => $range_id,
                        'range_type'   => $range_type,
                        'folder_type'  => $folder_type,
                        'name'         => '',
                        'data_content' => '',
                        'description'  => '',
                        'mkdate'       => strtotime($row['created']),
                        'chdate'       => $now
                    ];
                    $stmt_folder->execute($values);
                    $folder_id[$row['object_id'] . ':' . $row['type']] = $new_folder_id;
                }

                $file_ref_id = md5($row['file_id'] . ':' . $row['object_id'] . ':' . uniqid('file_refs' , true));
                $values = [
                    'id'          => $file_ref_id,
                    'file_id'     => $row['file_id'],
                    'folder_id'   => $folder_id[$row['object_id'] . ':' . $row['type']],
                    'description' => '',
                    'user_id'     => $row['user_id'],
                    'name'        => $row['name'],
                    'mkdate'      => strtotime($row['created']),
                    'chdate'      => $now
                ];
                $stmt_file_ref->execute($values);
            }
        }
    }

    public function down()
    {
        $db = DBManager::get();

        // unregister core plugin
        $sql = "DELETE plugins, roles_plugins, tools_activated FROM plugins
                LEFT JOIN roles_plugins USING (pluginid)
                LEFT JOIN tools_activated ON plugin_id = pluginid
                WHERE pluginclassname = 'VipsModule'";
        $db->exec($sql);

        // update etask tables
        $sql = "ALTER TABLE etask_assignments
                CHANGE type type varchar(64) NOT NULL,
                CHANGE active active tinyint UNSIGNED NOT NULL,
                DROP weight,
                DROP block_id,
                DROP KEY test_id,
                DROP KEY range_id";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_assignment_attempts
                DROP ip_address,
                CHANGE options options text NOT NULL,
                DROP KEY assignment_id";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_responses
                CHANGE response response text NOT NULL,
                DROP student_comment,
                DROP ip_address,
                DROP commented_solution,
                DROP KEY assignment_id,
                DROP KEY user_id,
                DROP KEY task_id";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_tasks
                CHANGE type type varchar(64) NOT NULL,
                CHANGE description description text NOT NULL,
                CHANGE task task text NOT NULL,
                DROP KEY user_id";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_tests
                CHANGE description description text NOT NULL,
                CHANGE options options text NOT NULL,
                DROP KEY user_id";
        $db->exec($sql);

        $sql = "ALTER TABLE etask_test_tasks
                DROP part,
                DROP KEY task_id";
        $db->exec($sql);

        // drop new tables
        $db->exec('DROP TABLE etask_blocks, etask_group_members');

        // remove config entries
        $sql = "DELETE config, config_values
                FROM config
                LEFT JOIN config_values USING (field)
                WHERE field IN (
                    'VIPS_COURSE_GRADES',
                    'VIPS_EXAM_RESTRICTIONS',
                    'VIPS_EXAM_ROOMS',
                    'VIPS_EXAM_TERMS'
                )";
        $db->exec($sql);
    }
}
