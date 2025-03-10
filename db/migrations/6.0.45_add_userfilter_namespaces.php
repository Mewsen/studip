<?php

/**
 * Adds namespace values to UserFilter database entries that already existed pre-6.0.
 */
final class AddUserfilterNamespaces extends Migration
{

    public function description()
    {
        return 'Add namesspaces to existing UserFilter database entries';
    }

    public function up()
    {
        $types = [
            'DegreeCondition',
            'PermissionCondition',
            'SemesterOfStudyCondition',
            'StgteilVersionCondition',
            'SubjectCondition',
            'SubjectConditionAny'
        ];

        DBManager::get()->execute(
            "UPDATE `userfilter_fields`
             SET `type` = CONCAT('UserFilterFields\\\', `type`)
             WHERE `type` IN (:types)",
            ['types' => $types]
        );

        // Special handling for datafield entries which have an ID appended to the type name:
        DBManager::get()->execute(
            "UPDATE `userfilter_fields`
             SET `type` = CONCAT('UserFilterFields\\\', `type`)
             WHERE `type` LIKE :type",
            ['type' => 'DatafieldCondition_%']
        );
    }
}
