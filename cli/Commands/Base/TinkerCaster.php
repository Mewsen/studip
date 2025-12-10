<?php

namespace Studip\Cli\Commands\Base;

use Illuminate\Support\Collection;
use SimpleCollection;
use SimpleORMap;
use Symfony\Component\VarDumper\Caster\Caster;

class TinkerCaster
{
    /**
     * Get an array representing the properties of a collection.
     *
     * @param  SimpleCollection  $collection
     * @return array
     */
    public static function castCollection(Collection $collection)
    {
        return [
            Caster::PREFIX_VIRTUAL . 'all' => $collection->all(),
        ];
    }

    /**
     * Get an array representing the properties of a collection.
     *
     * @param  SimpleORMap $collection
     * @return array
     */
    public static function castModel(SimpleORMap $model)
    {
        return [
            Caster::PREFIX_VIRTUAL . 'attributes' => $model->toArray(),
        ];
    }

    /**
     * Get an array representing the properties of a collection.
     *
     * @param  SimpleCollection  $collection
     * @return array
     */
    public static function castSimpleCollection(SimpleCollection $collection)
    {
        return [
            Caster::PREFIX_VIRTUAL . 'all' => [...$collection],
        ];
    }
}
