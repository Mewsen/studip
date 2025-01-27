<?php

namespace MassMail;

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
