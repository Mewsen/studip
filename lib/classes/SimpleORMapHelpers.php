<?php

trait SimpleORMapHelpers
{
    /**
     * Retrieve the first record matching the attributes or create it.
     */
    public static function firstOrCreate(array $attributes, array $values = []): static
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

    /**
     * Update or create a record matching the attributes, and fill it with values.
     */
    public static function updateOrCreate(array $attributes, array $values = []): static
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

        return static::create([
            ...$attributes,
            ...$values
        ]);
    }
}
