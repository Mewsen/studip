<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/5946
 */
class CleanupWikiStartpageParent extends Migration
{
    public function up(): void
    {
        $query = "SELECT `range_id`, `value`
                  FROM `config_values`
                  WHERE `field` = 'WIKI_STARTPAGE_ID'";
        $pages = DBManager::get()->fetchAll($query);

        $query = "UPDATE `wiki_pages`
                  SET `parent_id` = NULL
                  WHERE `page_id` = ? AND `range_id` = ?";
        $statement = DBManager::get()->prepare($query);
        foreach ($pages as [$range_id, $page_id]) {
            $statement->execute([$page_id, $range_id]);
        }
    }
}
