<?php

namespace MassMail;

/**
 * @license GPL2 or any later version
 *
 * @property array $id alias for pk
 * @property int $message_id database column
 * @property string $filter_id database column
 * @property int $mkdate database column
 * @property-read mixed $userfilter additional field
 */
class MassMailFilter extends \SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'massmail_filter';

        $config['additional_fields']['userfilter']['get'] = function ($entry) {
            return new \UserFilter($entry->filter_id);
        };
        $config['registered_callbacks']['before_delete'][] = 'cbDeleteUserFilter';
        $config['registered_callbacks']['after_store'][] = 'cbUpdateUserFilterRange';

        parent::configure($config);
    }

    public function cbDeleteUserFilter()
    {
        $filter = new \UserFilter($this->filter_id);
        $filter->delete();
    }

    public function cbUpdateUserFilterRange()
    {
        $filter = new \UserFilter($this->filter_id);
        $filter->setRange(MassMailMessage::class, $this->message_id);
        $filter->store();
    }

}
