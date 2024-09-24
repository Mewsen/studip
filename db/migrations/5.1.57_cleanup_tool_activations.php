<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/3977
 */
return new class extends Migration
{
    public function description()
    {
        return 'Removes invalid tool activations (that are no longer connected '
            . 'to a StandardPlugin';
    }

    protected function up()
    {
        $query = "DELETE FROM `tools_activated`
                  WHERE `plugin_id` NOT IN (
                      SELECT `pluginid`
                      FROM `plugins`
                      WHERE FIND_IN_SET(?, `plugintype`)
                         OR FIND_IN_SET(?, `plugintype`)
                  )";
        DBManager::get()->execute($query, [StudipModule::class, StandardPlugin::class]);
    }
};
