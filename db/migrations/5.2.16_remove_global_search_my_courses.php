<?php
return new class extends Migration
{
    public function description()
    {
        return 'Remove global search for my courses';
    }

    protected function up()
    {
        foreach (['config', 'config_values'] as $table) {
            $this->removeConfiguration($table);
        }

        $query = "DELETE `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` = 'GLOBALSEARCHMODULES'
                    AND `config`.`value` = `config_values`.`value`";
        DBManager::get()->exec($query);
    }

    protected function down()
    {
        // We will not activate the search module since we cannot know it's
        // previous state
    }

    private function removeConfiguration(string $table): void
    {
        $query = "SELECT `value`
                  FROM `{$table}`
                  WHERE `field` = 'GLOBALSEARCH_MODULES'";
        $json = DBManager::get()->fetchColumn($query);

        if (!$json) {
            return;
        }

        $modules = json_decode($json, true);
        $modules = array_filter(
            $modules,
            function ($index) {
                return $index !== 'GlobalSearchMyCourses';
            },
            ARRAY_FILTER_USE_KEY
        );

        $query = "UPDATE `{$table}`
                  SET `value` = ?
                  WHERE `field` = 'GLOBALSEARCH_MODULES'";
        DBManager::get()->execute($query, [json_encode($modules)]);
    }
};
