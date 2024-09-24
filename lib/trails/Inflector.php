<?php
namespace Trails;

/**
 * The Inflector class is a namespace for inflections methods.
 *
 * @package       trails
 *
 * @author        mlunzena
 * @copyright (c) Authors
 * @version       $Id: trails.php 7001 2008-04-04 11:20:27Z mlunzena $
 */
class Inflector
{
    /**
     * Returns a camelized string from a lower case and underscored string by
     * replacing slash with underscore and upper-casing each letter preceded
     * by an underscore.
     *
     * @param string $word String to camelize.
     * @return string Camelized string.
     */
    public static function camelize(string $word): string
    {
        $parts = explode('/', $word);
        foreach ($parts as $key => $part) {
            $parts[$key] = strtopascalcase($part);
        }
        return implode('_', $parts);
    }

    /**
     * @param string $word
     * @return string
     */
    public static function underscore(string $word): string
    {
        $parts = explode('_', $word);
        foreach ($parts as $key => $part) {
            $parts[$key] = strtosnakecase($part);
        }
        return implode('/', $parts);
    }
}
