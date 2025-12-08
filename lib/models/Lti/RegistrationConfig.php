<?php
namespace Lti;

use SimpleORMap;

class RegistrationConfig extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_registration_configs';

        $config['belongs_to']['registration'] = [
            'class_name' => Registration::class,
            'foreign_key' => 'registration_id',
            'assoc_foreign_key' => 'id'
        ];

        parent::configure($config);
    }

    public static function updateOrCreate(array $attributes, array $values = []): self
    {
        $whereClauses = [];
        foreach ($attributes as $key => $value) {
            $whereClauses[] = "$key = :$key";
        }

        $record = static::findOneBySQL(implode(' AND ', $whereClauses), $attributes);

        if ($record) {
            $record->setData($values);
            $record->store();
            return $record;
        }

        return static::create(array_merge($attributes, $values));
    }
}
