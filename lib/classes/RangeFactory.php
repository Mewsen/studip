<?php
/**
 * Factory for ranges.
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since Stud.IP 4.1
 */
final class RangeFactory
{
    public const TYPE_MAPPING = [
        'sem'  => 'course',
        'user' => 'user',
        'inst' => 'institute',
        'fak'  => 'institute',
    ];

    /**
     * Finds a Range for a given id or false if there is no Range with the id.
     * @param string $id   Range id
     * @param array $search_types array can have values of 'sem', 'user', 'inst' and/or 'fak'
     * @return Range|false
     */
    public static function find(
        string $id,
        array $search_types = ['sem', 'user', 'inst', 'fak']
    ) {
        $type = get_object_type($id, $search_types);
        if ($type === false) {
            return false;
        }

        return self::createRange(self::TYPE_MAPPING[$type], $id);
    }

    /**
     * Create a range by given type and id.
     *
     * @param string $type Range type
     * @param mixed  $id   Range id
     * @return Range any of the supported range types
     * @throws Exception when an invalid range type was given
     */
    public static function createRange(string $type, string $id): Range
    {
        if ($type === 'user') {
            return new User($id);
        }

        if ($type === 'course') {
            return new Course($id);
        }

        if ($type === 'institute' || $type === 'fak') {
            return new Institute($id);
        }

        throw new Exception('Unknown type');
    }
}
