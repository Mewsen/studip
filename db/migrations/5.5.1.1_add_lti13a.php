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
            ADD COLUMN is_global TINYINT(1) NOT NULL DEFAULT '1',
            ADD COLUMN oidc_init_url VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN jwks_url VARCHAR(255) NOT NULL DEFAULT '',
            ADD COLUMN deep_linking_url VARCHAR(255) NOT NULL DEFAULT ''"
        );

        $this->migrateLtiDataTable();
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
            OPTIONS = :new_options
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
                        'new_options'   => $options ?? [],
                        'deployment_id' => $row['id']
                    ]
                );
            }
        }
    }

    protected function down()
    {
        //Uhhh... no!
    }
}
