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
        return $GLOBALS['perm']->have_perm('dozent', $user->id);
    }

    public static function canCreateCourseSets(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id)
            || (
                Config::get()->ALLOW_DOZENT_COURSESET_ADMIN
                && $GLOBALS['perm']->have_perm('dozent', $user->id)
            );
    }

    public static function canCreateAdmissionRules(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('dozent', $user->id);
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

            $intersection = array_intersect(
                array_keys($courseset->getInstituteIds()),
                $institutes
            );

            // Check access for admin (or dozent if permission is set) accounts.
            $access = $GLOBALS['perm']->have_perm(Config::get()->ALLOW_DOZENT_COURSESET_ADMIN ? 'dozent' : 'admin')
                && count($intersection) > 0;

            return $access;
        }
    }

}
