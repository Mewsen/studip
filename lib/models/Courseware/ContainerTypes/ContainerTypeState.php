<?php

namespace Courseware\ContainerTypes;

use DBManager;
use SimpleORMap;

/**
 * This class represents the activation state of a container type.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ContainerTypeState extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'cw_container_type_states';

        parent::configure($config);
    }

    /**
     * Returns all `ContainerType`s that are activated in this Stud.IP installation.
     *
     * @return iterable<string> an iterable of all `ContainerType` classes that are activated.
     */
    public static function getActivatedContainerTypes(): iterable
    {
        $args = [
            ContainerType::getContainerTypes(),
            DBManager::get()->fetchFirst(
                'SELECT `container_type` FROM `cw_container_type_states` WHERE `activated` = 0'
            ),
        ];

        return [...array_diff(...$args)];
    }

    /**
     * Activate the `ContainerType` this `ContainerTypeState` is relating to.
     *
     * @return `true` on success, `false` otherwise
     */
    public function activate(): bool
    {
        $this->activated = 1;
        return (bool) $this->store();
    }

    /**
     * Deactivate the `ContainerType` this `ContainerTypeState` is relating to.
     *
     * @return `true` on success, `false` otherwise
     */
    public function deactivate(): bool
    {
        $this->activated = 0;
        return (bool) $this->store();
    }
}
