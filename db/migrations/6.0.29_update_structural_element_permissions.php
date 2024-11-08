<?php

final class UpdateStructuralElementPermissions extends Migration
{
    public function description()
    {
        return 'Update structural element permissions for new settings';
    }
    public function up()
    {
        $query = "SELECT * FROM `cw_structural_elements` WHERE `visible_approval` != '[]' AND  `writable_approval` != '[]'";
        $rows_statement = DBManager::get()->prepare($query);
        $rows = $rows_statement->execute();

        $query = "UPDATE `cw_structural_elements`
                  SET 
                    `permission_type` = :permission_type,
                    `visible` = :visible,
                    `writable` = :writable
                    `visible_approval` = :visible_approval,
                    `writable_approval` = :writable_approval,
                  WHERE `id` = :id";
        $statement = DBManager::get()->prepare($query);

        foreach ($rows as $row) {
            $read_approval = json_decode($row['visible_approval'], true) ?: [];
            $write_approval =  json_decode($row['writable_approval'], true) ?: [];
            $permission_type = $row['permission_type'];
            $visible = $row['visible'];
            $writable = $row['writable'];
            $visible_approval = [];
            $writable_approval = [];
            if (!$read_approval['all'] && $write_approval['all']) {
                $writable = 'always';
            }

            if (count($read_approval['groups']) || count($write_approval['groups'])) {
                $permission_type = 'groups';
                $writable = 'always';
                $visible_approval = $read_approval['groups'];
                $writable_approval = $write_approval['groups'];
            }
            if (count($read_approval['users']) || count($write_approval['users'])) {
                $permission_type = 'users';
                $writable = 'always';
                $visible_approval = $read_approval['users'];
                $writable_approval = $write_approval['users'];
            }

            $statement->bindValue(':permission_type', $permission_type);
            $statement->bindValue(':visible', $visible);
            $statement->bindValue(':writable', $writable);
            $statement->bindValue(':visible_approval', json_encode($visible_approval));
            $statement->bindValue(':writable_approval', json_encode($writable_approval));
            $statement->execute();
        }

        $query = "SELECT * FROM `cw_structural_elements` WHERE `visible_start_date` IS NOT NULL OR  `visible_end_date` IS NOT NULL";
        $rows_statement = DBManager::get()->prepare($query);
        $rows = $rows_statement->execute();

        $query = "UPDATE `cw_structural_elements`
                  SET 
                    `visible` = :visible,
                  WHERE `id` = :id";
        $statement = DBManager::get()->prepare($query);

        foreach ($rows as $row) {
            $visible = 'period';
            
            $statement->bindValue(':visible', $visible);
            $statement->execute();
        }
    }
}