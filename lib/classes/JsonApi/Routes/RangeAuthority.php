<?php

namespace JsonApi\Routes;

use Course;
use Institute;
use Range;
use User;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use JsonApi\Routes\Institutes\Authority as InstituteAuthority;

class RangeAuthority
{
    const SCOPE_BASIC = 'basic';

    public static function canShowRange(User $user, Range $range, $scope = self::SCOPE_BASIC): bool
    {
        if ($range instanceof Course) {
            return CourseAuthority::canShowCourse($user, $range, $scope);
        }

        if ($range instanceof Institute) {
            return InstituteAuthority::canShowInstitute($user, $range);
        }

        return false;
    }

    public static function canEditRange(User $user, Range $range): bool
    {
        if ($range instanceof Course) {
            return CourseAuthority::canEditCourse($user, $range);
        }

        if ($range instanceof Institute) {
            return InstituteAuthority::canEditInstitute($user, $range);
        }

        return false;
    }
}
