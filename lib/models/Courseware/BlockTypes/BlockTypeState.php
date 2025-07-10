<?php

namespace Courseware\BlockTypes;

use DBManager;
use SimpleORMap;

/**
 * This class represents the activation state of a block type.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BlockTypeState extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'cw_block_type_states';

        parent::configure($config);
    }

    /**
     * Returns all `BlockType`s that are activated in this Stud.IP installation.
     *
     * @return iterable<string> an iterable of all `BlockType` classes that are activated.
     */
    public static function getActivatedBlockTypes(): iterable
    {
        $args = [
            BlockType::getBlockTypes(),
            DBManager::get()->fetchFirst('SELECT `block_type` FROM `cw_block_type_states` WHERE `activated` = 0'),
        ];

        return [...array_diff(...$args)];
    }

    /**
     * Activate the `BlockType` this `BlockTypeState` is relating to.
     *
     * @return `true` on success, `false` otherwise
     */
    public function activate(): bool
    {
        $this->activated = 1;
        return (bool) $this->store();
    }

    /**
     * Deactivate the `BlockType` this `BlockTypeState` is relating to.
     *
     * @return `true` on success, `false` otherwise
     */
    public function deactivate(): bool
    {
        $this->activated = 0;
        return (bool) $this->store();
    }
}
