<?php

/*
 *  Copyright (c) 2012  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

/**
 * Class to define and manage attributes of seminar types.
 * Usually all sem-types are stored in a global variable $SEM_TYPE which is
 * an array of SemType objects.
 *
 * SemType::getTypes() gets you all seminar types in an array.
 *
 * This class only represents the name of the type and gives a relation to a
 * sem_class.
 */
class SemType extends SimpleORMap
{
    static protected $sem_types = null;

    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'sem_types';

        $config['i18n_fields']['name'] = true;

        $config['has_many']['courses'] = [
            'class_name'        => Course::class,
            'assoc_foreign_key' => 'status'
        ];

        $config['belongs_to']['sem_class'] = [
            'class_name'  => SemClass::class,
            'foreign_key' => 'class'
        ];

        $config['registered_callbacks']['after_store'][] = 'SemType::refreshTypes';
        $config['registered_callbacks']['after_delete'][] = 'SemType::refreshTypes';

        parent::configure($config);
    }

    /**
     * Returns the number of seminars of this sem_type in Stud.IP
     * @return integer
     */
    public function countSeminars()
    {
        return Course::countBySql('status = ?', [$this->id]);
    }

    public function getSemClass()
    {
        return $GLOBALS['SEM_CLASS'][$this->class] ?? SemClass::getDefaultSemClass();
    }

    /**
     * Returns an array of all SemTypes in Stud.IP. Equivalent to global
     * $SEM_TYPE variable. This variable is statically stored in this class.
     * @return array of SemType
     */
    public static function getTypes()
    {
        if (!is_array(self::$sem_types)) {
            $cache = \Studip\Cache\Factory::getCache();
            $types_array = $cache->read('DB_SEM_TYPES_ARRAY');

            if (!is_array($types_array)) {
                $types_array = self::findBySQL('1 ORDER BY id');
                $cache->write('DB_SEM_TYPES_ARRAY', $types_array);
            }

            foreach ($types_array as $sem_type) {
                self::$sem_types[$sem_type->id] = $sem_type;
            }
        }

        return self::$sem_types;
    }

    public static function refreshTypes()
    {
        \Studip\Cache\Factory::getCache()->expire('DB_SEM_TYPES_ARRAY');
        self::$sem_types = null;
        return self::getTypes();
    }

    /**
     * Gets all SemTypes that are allowed as group parents.
     * @return array
     */
    public static function getGroupingSemTypes()
    {
        return SimpleCollection::createFromArray(array_flatten(SemClass::getGroupClasses()->getSemTypes()))->pluck('id');
    }

    /**
     * Gets all SemTypes that are allowed as group parents.
     * @return array
     */
    public static function getNonGroupingSemTypes()
    {
        $non_grouping = SimpleCollection::createFromArray(SemClass::getClasses())->findBy('is_group', false)->findBy('studygroup_mode', false);
        return SimpleCollection::createFromArray(array_flatten($non_grouping->getSemTypes()))->pluck('id');
    }
}
