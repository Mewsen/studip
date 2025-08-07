<?php

trait IconNavigationTrait
{

    public static array $nav_cache = [];

    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        /** @var StudipModuleExtended $this */
        if (!array_key_exists($course_id, self::$nav_cache)) {
            $navs = $this->getManyIconNavigation([$course_id], $user_id);
            self::$nav_cache[$course_id] = $navs[$course_id] ?? null;
        }

        return self::$nav_cache[$course_id];
    }

}
