<?php

namespace Studip\Cli\Commands\Base;

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
    public static function castCollection(SimpleCollection $collection)
    {
        return [
            Caster::PREFIX_VIRTUAL . 'all' => [...$collection],
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
}
