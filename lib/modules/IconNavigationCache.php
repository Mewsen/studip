<?php
/**
 * This cache is only used in the IconNavigationTrait. It stores the navigations
 * for a user and a course.
 *
 * It is designed to be disabled when not wanted to reduce memory usage.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since Stud.IP 6.1
 */
final class IconNavigationCache
{
    private static bool $enabled = true;

    /** @var array<string, array<string, ?Navigation>> */
    private static array $cache = [];

    /**
     * Enables or disables the cache.
     *
     * @return bool Previous state
     */
    public static function setEnabled(bool $enabled): bool
    {
        $previous = self::$enabled;
        self::$enabled = $enabled;
        return $previous;
    }

    /**
     * Clear the cache, optionally for a specific user or course.
     */
    public static function clear(?string $user_id = null, ?string $course_id = null): void
    {
        if ($user_id !== null && $course_id !== null) {
            unset(self::$cache[$user_id][$course_id]);
        } elseif ($user_id !== null) {
            unset(self::$cache[$user_id]);
        } elseif ($course_id !== null) {
            self::$cache = array_map(
                fn(array $user_cache) => array_filter(
                    $user_cache,
                    fn(string $id): bool => $id !== $course_id,
                    ARRAY_FILTER_USE_KEY
                ),
                self::$cache
            );
            self::$cache = array_filter(
                self::$cache,
                fn(array $user_cache): bool => $user_cache !== [],
            );
        } else {
            self::$cache = [];
        }
    }

    public static function has(string $user_id, string $course_id): bool
    {
        if (!self::$enabled) {
            return false;
        }
        return array_key_exists($course_id, self::$cache[$user_id] ?? []);
    }

    public static function get(string $user_id, string $course_id): ?Navigation
    {
        if (!self::$enabled) {
            return null;
        }
        return self::$cache[$user_id][$course_id] ?? null;
    }

    public static function set(string $user_id, string $course_id, ?Navigation $nav): ?Navigation
    {
        if (!self::$enabled) {
            return $nav;
        }

        self::$cache[$user_id] ??= [];

        return self::$cache[$user_id][$course_id] = $nav;
    }
}
