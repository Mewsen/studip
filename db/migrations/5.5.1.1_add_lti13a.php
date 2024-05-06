<?php


class AddLti13a extends Migration
{
    public function description()
    {
        return 'Add tables and settings for the LTI 1.3A functionality.';
    }

    protected function up()
    {
        $db = DBManager::get();

        $db->exec(
            "CREATE TABLE IF NOT EXISTS keyrings (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                range_id CHAR(32) NOT NULL,
                range_type VARCHAR(16) NOT NULL,
                public_key BLOB(16384) NOT NULL,
                private_key BLOB(16384) NOT NULL DEFAULT '',
                passphrase VARCHAR(512) NOT NULL DEFAULT '',
                mkdate BIGINT(10) NOT NULL DEFAULT '0',
                chdate BIGINT(10) NOT NULL DEFAULT '0'
            )"
        );
        $db->exec("ALTER TABLE `keyrings` ADD INDEX(`range_id`, `range_type`)");

        /*
        $db->exec(
            "CREATE TABLE IF NOT EXISTS lti_registrations (
                id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                client_id BIGINT NOT NULL,
                tool_id CHAR(32) NOT NULL,
                mkdate BIGINT(10) NOT NULL DEFAULT '0',
                chdate BIGINT(10) NOT NULL DEFAULT '0'
            )"
        );
        */

        $db->exec("RENAME TABLE `lti_tool` TO lti_tools");

        $db->exec(
            "ALTER TABLE `lti_tools`
            ADD COLUMN lti_version VARCHAR(8) NOT NULL DEFAULT '1.3a',
            ADD COLUMN is_global TINYINT(1) NOT NULL DEFAULT '0',
            ADD COLUMN oidc_init_url VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN jwks_url VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN jwks_key_id VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN deep_linking_url VARCHAR(255) NOT NULL DEFAULT ''"
        );

        $this->migrateLtiDataTable();

        $this->addConfig();
    }

    protected function migrateLtiDataTable()
    {
        $db = DBManager::get();
        $db->exec("RENAME TABLE `lti_data` TO lti_deployments");

        //Create LTI tool instances for the old LTI 1.0/1.1 tools
        //that have been configured directly in a course:
        $stmt = $db->prepare(
            "SELECT `id`, `tool_id`, `title`, `options`
            FROM `lti_deployments`
            WHERE `tool_id` = '0'"
        );
        $update_stmt = $db->prepare(
            "UPDATE `lti_deployments`
            SET `tool_id` = :new_tool_id,
            `options` = :new_options
            WHERE `id` = :deployment_id"
        );
        $create_tool_stmt = $db->prepare(
            "INSERT INTO `lti_tools`
            (`id`, `name`, `launch_url`, `consumer_key`, `consumer_secret`,
            `custom_parameters`, `send_lis_person`, `lti_version`, `is_global`,
            `mkdate`, `chdate`)
            VALUES
            (:id, :name, :launch_url, :consumer_key, :consumer_secret,
            :custom_parameters, :send_lis_person, '1.1', '0',
            UNIX_TIMESTAMP(), UNIX_TIMESTAMP())"
        );
        $new_tool_id_stmt = $db->prepare("SELECT MAX(`id`) + 1 FROM `lti_tools`");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (empty($row['id']) || empty($row['title']) || empty($row['options'])) {
                //That tool cannot be migrated.
                continue;
            }
            //Create a new tool and migrate the data from the options field:
            $options = json_decode($row['options'], true);
            $new_tool_id_stmt->execute();
            $new_tool_id = $new_tool_id_stmt->fetchColumn();
            $success = $create_tool_stmt->execute(
                [
                    'id'           => $new_tool_id,
                    'name'         => $row['title'],
                    'launch_url'   => $options['launch_url'] ?? '',
                    'consumer_key' => $options['consumer_key'] ?? '',
                    'consumer_secret' => $options['consumer_secret'] ?? '',
                    'custom_parameters' => $options['custom_parameters'] ?? '',
                    'send_lis_person'   => $options['send_lis_person'] ?? '0'
                ]
            );
            unset($options['launch_url']);
            unset($options['consumer_key']);
            unset($options['consumer_secret']);
            unset($options['custom_parameters']);
            unset($options['send_lis_person']);
            if ($success) {
                $update_stmt->execute(
                    [
                        'new_tool_id'   => $new_tool_id,
                        'new_options'   => json_encode($options ?? []),
                        'deployment_id' => $row['id']
                    ]
                );
            }
        }
    }

    protected function addConfig()
    {
        $db = DBManager::get();

        $configs = [
            [
                'ENABLE_COURSES_AS_LTI_TOOLS', '0', 'boolean', 'global',
                'Sollen Veranstaltungen über die LTI 1.3a Schnittstelle als LTI-Tool angeboten werden können?'
            ],
            [
                'LTI_SHARING_ENABLED', '0', 'boolean', 'course',
                'Darf die Veranstaltung als LTI-Tool angeboten werden?'
            ],
            [
                'LTI_DATA_PROTECTION_DEFAULT_WARNING',
                'Sie verlassen jetzt Stud.IP. Vorsicht mit den persönlichen Daten!', //TODO
                'string',
                'global',
                'Eine Warnung zur Weitergabe personenbezogener Daten, die standardmäßig angezeigt wird, wenn Personen aus einer Veranstaltung in ein LTI-Tool wechselt.'
            ],
            [
                'LTI_DATA_PROTECTION_COURSE_WARNING',
                '',
                'string',
                'course',
                'Eine in einer Veranstaltung angepasste Warnung zur Weitergabe personenbezogener Daten, die angezeigt wird, wenn Personen aus der Veranstaltung in ein LTI-Tool wechselt.'
            ]
        ];

        $stmt = $db->prepare(
            "INSERT INTO `config`
            (`field`, `value`, `type`, `range`, `description`, `section`, `mkdate`, `chdate`)
            VALUES
            (:field, :value, :type, :range, :description, 'LTI', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())"
        );

        foreach ($configs as $c) {
            $stmt->execute(
                [
                    'field'       => $c[0],
                    'value'       => $c[1],
                    'type'        => $c[2],
                    'range'       => $c[3],
                    'description' => $c[4]
                ]
            );
        }
    }

    protected function down()
    {
        //Uhhh... no!
    }
}
