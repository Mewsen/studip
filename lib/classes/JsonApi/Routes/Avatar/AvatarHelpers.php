<?php

namespace JsonApi\Routes\Avatar;

use JsonApi\Errors\RecordNotFoundException;

trait AvatarHelpers
{

    protected static function getAvatarClass(String $range_id, String $range_type, \User $user): Array
    {
        $has_perm = false;
        $class = null;

        if ($range_type === 'users') {
            $has_perm = Authority::canUpdateAvatarOfUser($user);
            $class = \Avatar::class;
        } else if ($range_type === 'institutes') {
            $inst = \Institute::find($range_id);
            if ($inst) {
                $has_perm = Authority::canUpdateAvatarOfInstitute($user, $inst);
                $class = \InstituteAvatar::class;
            }
        } else if ($range_type === 'courses') {
            $course = \Course::find($range_id);
            if ($course) {
                $has_perm = Authority::canUpdateAvatarOfSeminar($user, $course);
                if ($course->isStudygroup()) {
                    $class = \StudygroupAvatar::class;
                } else {
                    $class = \CourseAvatar::class;
                }
            }
        } else {
            throw new RecordNotFoundException();
        }

        return ['class' => $class, 'has_perm' => $has_perm];
    }
}