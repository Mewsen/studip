<?php
 return new class extends Migration
 {
     protected function up()
     {
         $query = "ALTER TABLE `cronjobs_schedules`
                   DROP COLUMN `priority`,
                   DROP COLUMN `type`";
         DBManager::get()->exec($query);
     }

     protected function down()
     {
         $query = "ALTER TABLE `cronjobs_schedules`
                   ADD COLUMN `priority` ENUM('low', 'normal', 'high') COLLATE `latin1_bin` DEFAULT NULL AFTER `parameters`,
                   ADD COLUMN `type` ENUM('periodic', 'once') COLLATE `latin1_bin` DEFAULT NULL AFTER `priority`";
         DBManager::get()->exec($query);
     }
 };
