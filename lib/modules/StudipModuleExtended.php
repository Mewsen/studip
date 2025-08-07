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
     * Returning null for a course will result in a blank space, while returning no entry will render nothing.
     *
     * @param array $course_ids array of course or institute range ids.
     *      Only ranges where the module is active should be given
     * @param string|null $user_id the user to get the navigation for
     *
     * @return Navigation[] associative array per given course, containing a navigation or null,
     * where the course_id is the key: ['course_id_1' => $nav1, 'course_id_2' => $nav2, 'course_id_3' => null, ...].
     *
     */
    public function getManyIconNavigation(array $course_ids, ?string $user_id = null): array;

}
