<?php
namespace Lti;

use LtiResourceLink;
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

        $config['has_many']['resource_links'] = [
            'class_name' => LtiResourceLink::class,
            'assoc_foreign_key' => 'deployment_id',
            'on_delete' => 'delete'
        ];

        parent::configure($config);
    }

    public function transformData($with = []): array
    {
        $resourceLink = $this->resource_links[0];

        $base = [
            ...$this->toRawArray(),
            'range_id' => $resourceLink?->course_id,
            'range_name' => $resourceLink?->course->getFullName(),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];

        if (in_array('registration', $with)) {
            $base['registration'] = $this->registration->transformData();
        }

        return $base;
    }
}
