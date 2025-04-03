<?php


class AddLti13aToolModeTables extends Migration
{
    public function description()
    {
        return 'Adds tables and configuration entries for using Stud.IP as LTI 1.3A tool.';
    }

    protected function up()
    {
        $db = DBManager::get();

        $db->exec(
            "CREATE TABLE IF NOT EXISTS lti_platforms (
            id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            range_id CHAR(32) NOT NULL,
            name VARCHAR(255) NOT NULL,
            platform_id VARCHAR(255) NOT NULL,
            oauth2_access_token_url VARCHAR(255) NOT NULL,
            oidc_init_url VARCHAR(255) NOT NULL,
            jwks_url VARCHAR(255) NOT NULL,
            jwks_key_id VARCHAR(255) NOT NULL,
            mkdate INT(10) UNSIGNED NOT NULL DEFAULT 0,
            chdate INT(10) UNSIGNED NOT NULL DEFAULT 0
            )"
        );
        $db->exec(
            "ALTER TABLE `lti_platforms` ADD INDEX (`range_id`)"
        );

        $add_config_stmt = $db->prepare(
            "INSERT INTO `config`
            (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
            VALUES
            (:field, :value, :type, :range, :section, :mkdate, :chdate, :description)"
        );

        $add_config_stmt->execute([
            'field'       => 'ENABLE_SHARING_COURSES_AS_LTI_TOOLS',
            'value'       => '0',
            'type'        => 'boolean',
            'range'       => 'global',
            'section'     => 'LTI',
            'mkdate'      => time(),
            'chdate'      => time(),
            'description' => 'Sollen Veranstaltungen als LTI-Tools freigegeben werden können?'
        ]);

        $add_config_stmt->execute([
            'field'       => 'SHARE_COURSE_AS_LTI_TOOL',
            'value'       => '0',
            'type'        => 'boolean',
            'range'       => 'course',
            'section'     => 'LTI',
            'mkdate'      => time(),
            'chdate'      => time(),
            'description' => 'Soll die Veranstaltung als LTI-Tool freigegeben werden?'
        ]);

        $add_config_stmt->execute([
            'field'       => 'LTI_TOOL_ENTRY_POINT',
            'value'       => '0',
            'type'        => 'integer',
            'range'       => 'course',
            'section'     => 'LTI',
            'mkdate'      => time(),
            'chdate'      => time(),
            'description' => 'Welcher Reiter soll angezeigt werden, wenn eine Veranstaltung als LTI-Tool aufgerufen wird?'
        ]);
    }

    protected function down()
    {
        $db = DBManager::get();

        $db->exec(
            "DELETE FROM `config_values`
            WHERE `field` IN
                (
                'ENABLE_SHARING_COURSES_AS_LTI_TOOLS',
                'SHARE_COURSE_AS_LTI_TOOL',
                'LTI_TOOL_ENTRY_POINT'
                )"
        );
        $db->exec(
            "DELETE FROM `config`
            WHERE `field` IN
                (
                'ENABLE_SHARING_COURSES_AS_LTI_TOOLS',
                'SHARE_COURSE_AS_LTI_TOOL',
                'LTI_TOOL_ENTRY_POINT'
                )"
        );

        $db->exec("DROP TABLE IF EXISTS lti_platforms");
    }
}
