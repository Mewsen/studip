<?php

namespace JsonApi\Routes\Admission;

use Config;
use CourseSet;
use Institute;
use User;

class Authority
{

    /**
     * Checks if the given user may create a courseset. As this is provided as "quick action" inside of courses,
     * dozent permissions are sufficient.
     * @param User $user
     * @return bool
     */
    public static function canCreateCourseSet(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('dozent');
    }

    public static function canCreateCourseSets(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id)
            || (
                Config::get()->ALLOW_DOZENT_COURSESET_ADMIN
                && $GLOBALS['perm']->have_perm('dozent', $user->id)
            );
    }

    public static function canEditAdmissionRules(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id)
            || (
                Config::get()->ALLOW_DOZENT_COURSESET_ADMIN
                && $GLOBALS['perm']->have_perm('dozent', $user->id)
            );
    }

    /**
     * Checks if the given user may update the given courseset.
     *
     * @param User $user
     * @param CourseSet $courseset
     * @return bool
     */
    public static function canUpdateCourseSet(User $user, CourseSet $courseset)
    {
        if ($GLOBALS['perm']->have_perm('root') || $courseset->getUserId() === $user->id) {
            return true;
        } else {
            $institutes = array_map(
                fn ($i) => $i['Institut_id'],
                Institute::getMyInstitutes($user->id)
            );

            // Check access for admin accounts.
            $access = $GLOBALS['perm']->have_perm('admin')
                && array_intersect($courseset->getInstituteIds(), $institutes);

            if (!$access) {

                // Check access for lecturers if the config option is set.
                $access = Config::get()->ALLOW_DOZENT_COURSESET_ADMIN
                    && $GLOBALS['perm']->have_perm('dozent')
                    && array_intersect($courseset->getInstituteIds(), $institutes);
            }

            return $access;
        }
    }

}
