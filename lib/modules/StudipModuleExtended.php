<?php

interface StudipModuleExtended extends StudipModule
{
    /**
     * Returns navigation objects representing this plugin
     * in the course overview table for every given course or institute.
     * The navigation object's title will not be shown,
     * only the image (and its associated attributes like 'title')
     * and the URL are actually used.
     *
     * By convention, new or changed plugin content is indicated
     * by a different icon and a corresponding tooltip.
     *
     * @param array $course_ids array of course or institute range ids
     * @param string|null $user_id the user to get the navigation for
     *
     * @return array associative array per given course, containing a navigation or null
     */
    public function getManyIconNavigation(array $course_ids, string $user_id = null): array;

}
