<?php
namespace MassWidget;
use UserFilter;

class MassWidgetFilter extends \SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'masswidget_filter';

        $config['additional_fields']['userfilter']['get'] = function ($entry) {
            return new UserFilter($entry->filter_id);
        };
        $config['registered_callbacks']['before_delete'][] = 'deleteUserFilter';
        $config['registered_callbacks']['after_store'][] = 'updateUserFilterRange';

        parent::configure($config);
    }

    public function deleteUserFilter(): void
    {
        $filter = new UserFilter($this->filter_id);
        $filter->delete();
    }

    public function updateUserFilterRange(): void
    {
        $filter = new UserFilter($this->filter_id);
        $filter->setRange(MassWidget::class, $this->masswidget_id);
        $filter->store();
    }
}
