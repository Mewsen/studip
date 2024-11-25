<?php

namespace UserFilterFields\MassMail;

use UserFilterFields\SemesterOfStudyCondition;

class MassMailSemesterOfStudyFilter extends SemesterOfStudyCondition
{
    // --- ATTRIBUTES ---
    public $valuesDbTable = 'user_studiengang';
    public $valuesDbIdField = 'semester';
    public $userDataDbTable = 'user_studiengang';
    public $userDataDbField = 'semester';

    /**
     * @see \UserFilterField::getTargets()
     */
    public static function getTargets()
    {
        return ['students'];
    }

    /**
     * @see UserFilterField::__construct
     */
    public function __construct($fieldId='')
    {
        parent::__construct($fieldId);
        $this->relations = [
            'MassMailDegreeFilter' => [
                'local_field' => 'abschluss_id',
                'foreign_field' => 'abschluss_id'
            ],
            'MassMailSubjectFilter' => [
                'local_field' => 'fach_id',
                'foreign_field' => 'fach_id'
            ]
        ];
        $this->validCompareOperators = [
            '>=' => _('mindestens'),
            '<=' => _('höchstens'),
            '=' => _('ist'),
            '!=' => _('ist nicht')
        ];
        if (isset(self::$cached_valid_values[static::class])) {
            $this->validValues = self::$cached_valid_values[static::class];
        } else {
            // Initialize to some value in case there are no semester numbers.
            $maxsem = 15;
            // Calculate the maximal available semester.
            $stmt = \DBManager::get()->query("SELECT MAX(" . $this->valuesDbIdField . ") AS maxsem " .
                "FROM `" . $this->valuesDbTable . "`");
            if ($current = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                if ($current['maxsem']) {
                    $maxsem = $current['maxsem'];
                }
            }
            for ($i = 1; $i <= $maxsem; $i++) {
                $this->validValues[$i] = $i;
            }
            self::$cached_valid_values[static::class] = $this->validValues;
        }
    }

    /**
     * Get this field's display name.
     *
     * @return String
     */
    public function getName()
    {
        return _('Fachsemester');
    }

}
