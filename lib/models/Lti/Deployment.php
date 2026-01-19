<?php
namespace Lti;

use SimpleORMap;
use SimpleORMapCollection;

/**
 * @property int $id
 * @property string $name
 * @property string $purpose
 * @property bool $is_default
 * @property string $deployment_key
 * @property string $client_id
 * @property int $mkdate
 * @property int $chdate
 * @property Registration $registration
 * @property ResourceLink $resource_links
 * @property SimpleORMapCollection<Grade> $grades
 */
class Deployment extends SimpleORMap
{
    protected static function configure($config = []): void
    {
        $config['db_table'] = 'lti_deployments';

        $config['belongs_to']['registration'] = [
            'class_name' => Registration::class,
            'foreign_key' => 'registration_id',
            'assoc_foreign_key' => 'id',
        ];

        $config['has_many']['resource_links'] = [
            'class_name' => ResourceLink::class,
            'assoc_foreign_key' => 'deployment_id',
            'on_delete' => 'delete'
        ];

        // TODO:: rename link_id to deployment_id
        $config['has_many']['grades'] = [
            'class_name' => Grade::class,
            'assoc_foreign_key' => 'link_id',
            'on_delete' => 'delete'
        ];

        parent::configure($config);
    }

    public function transformData($with = []): array
    {
        $resourceLink = $this->resource_links[0];

        $base = [
            ...$this->toRawArray(),
            'is_default' => (bool) $this->is_default,
            'resource_id' => $resourceLink?->course_id,
            'resource_name' => $resourceLink?->course->getFullName(),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];

        if (in_array('registration', $with)) {
            $base['registration'] = $this->registration->transformData();
        }

        return $base;
    }

    /**
     * Get the custom_parameters of this entry.
     * TODO:: refactor this method
     *
     * @deprecated
     */
    public function getCustomParameters()
    {
        $parameters = '';
        if (!empty($this->registration->config_values['custom_parameters'])) {
            $parameters .= $this->registration->config_values['custom_parameters'] . "\n";
        }
        $parameters .= $this->options['custom_parameters'] ?? '';
        return $parameters;
    }

    public static function firstOrCreate(array $attributes, array $values = []): self
    {
        $whereClauses = [];
        foreach ($attributes as $key => $value) {
            $whereClauses[] = "$key = :$key";
        }

        $record = static::findOneBySQL(implode(' AND ', $whereClauses), $attributes);
        if ($record) {
            return $record;
        }

        return static::create([
            ...$attributes,
            ...$values
        ]);
    }
}
