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
 * Class to define and manage attributes of seminar classes (or seminar categories).
 * Usually all sem-classes are stored in a global variable $SEM_CLASS which is
 * an array of SemClass objects.
 *
 * SemClass::getClasses() gets you all seminar classes in an array.
 *
 * You can access the attributes of a sem-class like an associative
 * array with $sem_class['default_read_level']. The uinderlying data is stored
 * in the database in the table sem_classes.
 *
 * If you want to have a name of a sem-class like "Lehre", please use
 * $sem_class['name'] and you will get a fully localized name and not the pure
 * database entry.
 *
 * This class manages also which modules are contained in which course-slots,
 * like "what module is used as a forum in my seminars". In the database stored
 * is the name of the module like "CoreForum" or a classname of a plugin or null
 * if the forum is completely disabled by root for this sem-class. Core-modules
 * can only be used within a standard slot. Plugins may also be used as optional
 * modules not contained in a slot.
 *
 * In the field 'modules' in the database is for each modules stored in a json-string
 * if the module is activatable by the teacher or not and if it is activated as
 * a default. Please use the methods SemClass::isSlotModule, SemClass::getSlotModule,
 * SemClass::isModuleAllowed, SemClass::isModuleMandatory, SemClass::isSlotMandatory
 * or even more simple SemClass::getNavigationForSlot (see documentation there).
 */
class SemClass extends SimpleORMap
{
    static protected $sem_classes = null;

    static protected $studygroup_forbidden_modules = [
        'CoreAdmin',
        'CoreParticipants',
    ];

    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'sem_classes';

        $config['serialized_fields']['modules'] = 'JSONArrayObject';

        $config['has_many']['sem_types'] = [
            'class_name'        => SemType::class,
            'assoc_foreign_key' => 'class',
            'on_delete'         => 'delete'
        ];

        $config['registered_callbacks']['after_store'][] = 'SemClass::refreshClasses';
        $config['registered_callbacks']['after_delete'][] = 'SemClass::refreshClasses';

        parent::configure($config);
    }

    public static function getDefaultSemClass() {
        return self::build([
            'name' => _('Fehlerhafte Seminarklasse'),
            'modules' => [
                'CoreOverview' => ['activated' => 1, 'sticky' => 1],
                'CoreAdmin'    => ['activated' => 1, 'sticky' => 1]
            ],
            'visible' => 1,
            'is_group' => false
        ]);
    }

    /**
     * Generates a dummy SemClass for institutes of this type (as defined in config.inc.php).
     * @param integer $type   institute type
     * @return SemClass
     */
    public static function getDefaultInstituteClass($type)
    {
        global $INST_MODULES;

        // fall back to 'default' if modules are not defined
        $type = isset($INST_MODULES[$type]) ? $type : 'default';

        $data = [
            'name'                => _('Generierte Standardinstitutsklasse'),
            'visible'             => 1,
            'admin'               => 'CoreAdmin',     // always available
            'overview'            => 'CoreOverview'   // always available
        ];
        $slots = [
            'forum'               => 'CoreForum',
            'documents'           => 'CoreDocuments',
            'scm'                 => 'CoreScm',
            'wiki'                => 'CoreWiki',
            'calendar'            => 'CoreCalendar',
            'elearning_interface' => 'CoreElearningInterface',
            'personal'            => 'CorePersonal'
        ];
        $modules = [
            'CoreAdmin'           => ['activated' => 1, 'sticky' => 1],
            'CoreOverview'        => ['activated' => 1, 'sticky' => 1],
        ];

        foreach ($slots as $slot => $module) {
            $data[$slot] = $module;
            $modules[$module] = ['activated' => (int) ($INST_MODULES[$type][$slot] ?? 0), 'sticky' => 0];
        }
        $data['modules'] = $modules;

        return self::build($data);
    }

    /**
     * @param string $module
     * @return false|int
     */
    public function activateModuleInCourses($module)
    {
        $plugin = PluginManager::getInstance()->getPlugin($module);
        if ($plugin) {
            return Course::findEachBySQL(function ($course) use ($plugin) {
                return PluginManager::getInstance()->setPluginActivated($plugin->getPluginId(), $course->id, true);
            },
                "seminare.status IN (?)",
                [array_keys($this->getSemTypes())]);
        } else {
            return false;
        }
    }

    /**
     * @param string $module
     * @return false|int
     */
    public function deActivateModuleInCourses($module)
    {
        $plugin = PluginManager::getInstance()->getPlugin($module);
        if ($plugin) {
            return Course::findEachBySQL(function ($course) use ($plugin) {
                return PluginManager::getInstance()->setPluginActivated($plugin->getPluginId(), $course->id, false);
            },
                "seminare.status IN (?)",
                [array_keys($this->getSemTypes())]);
        } else {
            return false;
        }

    }

    /**
     * Returns the number of seminars of this sem_class in Stud.IP
     * @return integer
     */
    public function countSeminars()
    {
        $sum = 0;
        foreach ($this->sem_types as $sem_type) {
            $sum += $sem_type->countSeminars();
        }
        return $sum;
    }


    /**
     * @param string $modulename
     * @return bool
     */
    public function isModuleForbidden($modulename)
    {
        if (!empty($this->studygroup_mode)) {
            return in_array($modulename, self::$studygroup_forbidden_modules);
        } else {
            return strpos($modulename, 'Studygroup') !== false;
        }
    }

    /**
     * Returns the metadata of a module regarding this sem_class object.
     * @param string $modulename
     * @return array('sticky' => (bool), 'activated' => (bool))
     */
    public function getModuleMetadata($modulename)
    {
        return $this->modules[$modulename];
    }

    /**
     * @return StudipModule[]
     */
    public function getModuleObjects()
    {
        $result = [];
        foreach (array_keys($this->modules->getArrayCopy()) as $module) {
            $plugin = PluginManager::getInstance()->getPlugin($module);
            if ($plugin) {
                $result[$plugin->getPluginId()] = $plugin;
            }
        }
        return $result;
    }

    /**
     * @return string[]
     */
    public function getActivatedModules()
    {
        return array_keys(array_filter($this->modules->getArrayCopy(), function ($meta) {
            return $meta['activated'];
        }));
    }

    /**
     * @return StudipModule[]
     */
    public function getActivatedModuleObjects()
    {
        $result = [];
        foreach ($this->getActivatedModules() as $module) {
            $plugin = PluginManager::getInstance()->getPlugin($module);
            if ($plugin) {
                $result[$plugin->getPluginId()] = $plugin;
            }
        }
        return $result;
    }

    /**
     * @return mixed|object
     */
    public function getAdminModuleObject()
    {
        if ($this->studygroup_mode) {
            $module = 'CoreStudygroupAdmin';
        } else {
            $module = 'CoreAdmin';
        }
        return PluginManager::getInstance()->getPlugin($module);
    }

    /**
     * Returns true if a module is activated on default for this sem_class.
     * @param string $modulename
     * @return boolean
     */
    public function isModuleActivated($modulename)
    {
        return isset($this->modules[$modulename])
            && $this->modules[$modulename]['activated'];
    }

    /**
     * Returns if a module is allowed to be displayed for this sem_class.
     * @param string $modulename
     * @return boolean
     */
    public function isModuleAllowed($modulename)
    {
        return !$this->isModuleForbidden($modulename)
            && (
                empty($this->modules[$modulename])
                || empty($this->modules[$modulename]['sticky'])
                || !empty($this->modules[$modulename]['activated'])
            );
    }

    /**
     * Returns if a module is mandatory for this sem_class.
     * @param string $module
     * @return boolean
     */
    public function isModuleMandatory($module)
    {
        return isset($this->modules[$module])
            && !empty($this->modules[$module]['sticky'])
            && !empty($this->modules[$module]['activated']);
    }

    public function getSemTypes()
    {
        $types = [];
        foreach ($this->sem_types as $type) {
            $types[$type->id] = $type;
        }
        return $types;
    }

    /**
     * Checks if the current sem class is usable for course grouping.
     */
    public function isGroup()
    {
        return $this->is_group;
    }

    /**
     * Checks if any SemClasses exist that provide grouping functionality.
     * @return SimpleCollection
     */
    public static function getGroupClasses()
    {
        return SimpleCollection::createFromArray(self::getClasses())->findBy('is_group', true);
    }

    /**
     * Returns an array of all SemClasses in Stud.IP. Equivalent to global
     * $SEM_CLASS variable. This variable is statically stored in this class.
     * @return SemClass[] of SemClass
     */
    public static function getClasses()
    {
        if (!is_array(self::$sem_classes)) {
            $cache = \Studip\Cache\Factory::getCache();
            $class_array = $cache->read('DB_SEM_CLASSES_ARRAY');

            if (!is_array($class_array)) {
                $class_array = self::findBySQL('1 ORDER BY id');
                $cache->write('DB_SEM_CLASSES_ARRAY', $class_array);
            }

            foreach ($class_array as $sem_class) {
                self::$sem_classes[$sem_class->id] = $sem_class;
            }
        }

        return self::$sem_classes;
    }

    /**
     * Refreshes the internal $sem_classes cache-variable.
     * @return array of SemClass
     */
    public static function refreshClasses()
    {
        \Studip\Cache\Factory::getCache()->expire('DB_SEM_CLASSES_ARRAY');
        self::$sem_classes = null;
        return self::getClasses();
    }

    /**
     * Static method only to keep the translationstrings of the values. It is
     * never used within the system.
     */
    static private function localization()
    {
        _("Lehre");
        _("Forschung");
        _("Organisation");
        _("Community");
        _("Arbeitsgruppen");
        _("importierte Kurse");
        _("Hauptveranstaltungen");

        _("Hier finden Sie alle in Stud.IP registrierten Lehrveranstaltungen");
        _("Verwenden Sie diese Kategorie, um normale Lehrveranstaltungen anzulegen");
        _("Hier finden Sie virtuelle Veranstaltungen zum Thema Forschung an der Universität");
        _("In dieser Kategorie können Sie virtuelle Veranstaltungen für Forschungsprojekte anlegen.");
        _("Hier finden Sie virtuelle Veranstaltungen zu verschiedenen Gremien an der Universität");
        _("Um virtuelle Veranstaltungen für Uni-Gremien anzulegen, verwenden Sie diese Kategorie");
        _("Hier finden Sie virtuelle Veranstaltungen zu unterschiedlichen Themen");
        _("Wenn Sie Veranstaltungen als Diskussiongruppen zu unterschiedlichen Themen anlegen möchten, verwenden Sie diese Kategorie.");
        _("Hier finden Sie verschiedene Arbeitsgruppen an der %s");
        _("Verwenden Sie diese Kategorie, um unterschiedliche Arbeitsgruppen anzulegen.");
        _("Veranstaltungen dieser Kategorie dienen als Gruppierungselement, um die Zusammengehörigkeit von Veranstaltungen anderer Kategorien abzubilden.");
    }
}
