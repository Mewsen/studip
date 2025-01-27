<?php

namespace JsonApi\Routes\Mvv;

use Studiengang;
use Modul;
use User;

class Authority
{
    public static function canIndexCoursesOfStudy(User $user): bool
    {
        return true;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canShowCourseOfStudy(User $user, Studiengang $resource): bool
    {
        return $GLOBALS['perm']->have_perm('user') && self::isReadableStudyCourse($user, $resource);
    }

    private static function isReadableStudyCourse(User $user, Studiengang $resource)
    {
        $public_status = \ModuleManagementModel::getPublicStatus(Studiengang::class);
        return in_array($resource->stat, $public_status)
            || \RolePersistence::isAssignedRole($user->id, 'MVVAdmin');

    }

    public static function canIndexModules(User $user): bool
    {
        return true;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function canShowModule(User $user, Modul $resource): bool
    {
        return $GLOBALS['perm']->have_perm('user') && self::isReadableModule($user, $resource);
    }

    private static function isReadableModule(User $user, Modul $resource): bool
    {
        $public_status = \ModuleManagementModel::getPublicStatus(Modul::class);
        return in_array($resource->stat, $public_status)
            || \RolePersistence::isAssignedRole($user->id, 'MVVAdmin');

    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function canShowComponentVersion(User $user, \StgteilVersion $resource): bool
    {
        return $GLOBALS['perm']->have_perm('user') && self::isReadableComponentVersion($user, $resource);
    }

    private static function isReadableComponentVersion(User $user, \StgteilVersion $resource): bool
    {
        $public_status = \ModuleManagementModel::getPublicStatus(\StgteilVersion::class);
        return in_array($resource->stat, $public_status)
            || \RolePersistence::isAssignedRole($user->id, 'MVVAdmin');

    }
}
