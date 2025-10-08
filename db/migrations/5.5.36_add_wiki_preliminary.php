<?php

class AddWikiPreliminary extends Migration
{
    public function description(): string
    {
        return 'Add preliminary field to wiki_pages.';
    }

    protected function up(): void
    {
        DBManager::get()->exec(
            "ALTER TABLE `wiki_pages` ADD COLUMN `preliminary` TINYINT NOT NULL DEFAULT 0"
        );
    }

    protected function down(): void
    {
        DBManager::get()->exec(
            "ALTER TABLE `wiki_pages` DROP COLUMN `preliminary`"
        );
    }
}
