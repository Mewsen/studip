<?php
namespace Lti;

use SimpleORMap;

class Deployment extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_deployments';

        $config['belongs_to']['registration'] = [
            'class_name' => Registration::class,
            'foreign_key' => 'registration_id',
            'assoc_foreign_key' => 'id',
        ];

        parent::configure($config);
    }

    public function transformData($with = []): array
    {
        $base = [
            ...$this->toRawArray(),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];

        if (in_array('registration', $with)) {
            $base['registration'] = $this->registration->transformData();
        }

        return $base;
    }
}
