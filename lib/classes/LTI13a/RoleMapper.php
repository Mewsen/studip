<?php
namespace Studip\LTI13a;

final class RoleMapper {
    // Global
    const LTI_SYSTEM_ADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator';
    const LTI_USER = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#User';

    // Institution
    const LTI_INSTITUTION_ADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator';


    // Course
    const LTI_COURSE_ADMIN      = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Administrator';
    const LTI_COURSE_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor';
    const LTI_COURSE_TA         = 'http://purl.imsglobal.org/vocab/lis/v2/membership#TeachingAssistant';
    const LTI_COURSE_LEARNER    = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner';
    const LTI_COURSE_OBSERVER   = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Observer';

    public static function fromLocal(string $localRole): array
    {
        return match(strtolower($localRole)) {
            'root' => [
                self::LTI_SYSTEM_ADMIN,
                self::LTI_INSTITUTION_ADMIN,
                self::LTI_COURSE_ADMIN
            ],
            'admin' => [
                self::LTI_INSTITUTION_ADMIN,
                self::LTI_COURSE_ADMIN
            ],
            'dozent' => [
                self::LTI_COURSE_INSTRUCTOR,
                self::LTI_COURSE_TA
            ],
            'tutor' => [
                self::LTI_COURSE_TA
            ],
            'autor' => [
                self::LTI_COURSE_LEARNER,
                self::LTI_COURSE_OBSERVER
            ],
            'user' => [
                self::LTI_COURSE_OBSERVER
            ],
            default => []
        };
    }

    public static function toLocal(array $ltiRoles): array
    {
        $local = [
            'global' => null,
            'institut' => null,
            'course' => null
        ];

        // Global roles
        if (in_array(self::LTI_SYSTEM_ADMIN, $ltiRoles, true)) {
            $local['global'] = 'root';
        } elseif (in_array(self::LTI_USER, $ltiRoles, true)) {
            $local['global'] = 'user';
        }

        // Institution roles
        if (in_array(self::LTI_INSTITUTION_ADMIN, $ltiRoles, true)) {
            $local['institut'] = 'admin';
        }

        // Course roles
        if (in_array(self::LTI_COURSE_ADMIN, $ltiRoles, true)) {
            $local['course'] = 'admin';
        } elseif (in_array(self::LTI_COURSE_INSTRUCTOR, $ltiRoles, true)) {
            $local['course'] = 'dozent';
        } elseif (in_array(self::LTI_COURSE_TA, $ltiRoles, true)) {
            $local['course'] = 'tutor';
        } elseif (in_array(self::LTI_COURSE_LEARNER, $ltiRoles, true) || in_array(self::LTI_COURSE_OBSERVER, $ltiRoles, true)) {
            $local['course'] = 'autor';
        }

        return $local;
    }
}
