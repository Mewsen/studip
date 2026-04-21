<?php
/**
 * This trait is used for compatibility by implementing the getIconNavigation
 * method enforced by the StudipModule interface.
 *
 * @author Rami Jasim <rami.jasim@uol.de>
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 *
 * @mixin StudipModuleExtended
 * @phpstan-require-implements StudipModuleExtended
 */
trait IconNavigationTrait
{
    /**
     * @return Navigation|null
     */
    public function getIconNavigation($course_id, $last_visit, $user_id)
    {
        if (IconNavigationCache::has($user_id, $course_id, $this->getPluginId())) {
            return IconNavigationCache::get($user_id, $course_id, $this->getPluginId());
        }

        return IconNavigationCache::set(
            $user_id,
            $course_id,
            $this->getPluginId(),
            $this->getManyIconNavigation([$course_id], $user_id)[$course_id] ?? null
        );
    }
}
