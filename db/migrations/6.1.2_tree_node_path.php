<?php

return new class extends Migration
{
    public function description()
    {
        return 'Adds the path to the current node to all sem_tree and range_tree entries.';
    }

    public function up()
    {
        // Add the new database column for storing node ancestry path.
        DBManager::get()->exec("ALTER TABLE `sem_tree`
            ADD `ancestors` VARCHAR(255) NOT NULL AFTER `parent_id`,
            ADD INDEX `ancestors` (`ancestors`)"
        );
        StudipStudyArea::expireTableScheme();
        $this->buildStructure(StudipStudyArea::class, 'root', 0, 0, '');

        DBManager::get()->exec("ALTER TABLE `range_tree`
            ADD `ancestors` VARCHAR(255) NOT NULL AFTER `parent_id`,
            ADD INDEX `ancestors` (`ancestors`)"
        );
        RangeTreeNode::expireTableScheme();
        $this->buildStructure(RangeTreeNode::class, 'root', 0, 0, '');

        DBManager::get()->exec("ALTER TABLE `sem_tree`
            CHANGE `sem_tree_id` `sem_tree_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            CHANGE `parent_id` `parent_id` INT UNSIGNED NOT NULL");
        DBManager::get()->exec("ALTER TABLE `seminar_sem_tree`
            CHANGE `sem_tree_id` `sem_tree_id` INT UNSIGNED NOT NULL");
        DBManager::get()->exec("ALTER TABLE `range_tree`
            CHANGE `item_id` `item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            CHANGE `parent_id` `parent_id` INT UNSIGNED NOT NULL");
    }

    public function down()
    {
        DBManager::get()->exec("ALTER TABLE `sem_tree` DROP `ancestors`");
        DBManager::get()->exec("ALTER TABLE `range_tree` DROP `ancestors`");
    }

    private function buildStructure(string $classname, string $oldParentId, int $newParentId, int $currentId, string $ancestors)
    {
        foreach ($classname::findByParent_id($oldParentId, "ORDER BY `priority`") as $child) {
            $currentId++;

            $oldId = $child->id;
            $child->id = $currentId;
            $child->parent_id = $newParentId;
            $child->ancestors = $ancestors . ($ancestors === '' ? '' : '|') . $newParentId;
            $child->store();

            DBManager::get()->execute(
                "UPDATE `seminar_sem_tree` SET `sem_tree_id` = :new WHERE `sem_tree_id` = :old",
                ['new' => $child->id, 'old' => $oldId]
            );

            $currentId = $this->buildStructure($classname, $oldId, $child->id, $currentId, $child->ancestors);
        }

        return $currentId;
    }

};

