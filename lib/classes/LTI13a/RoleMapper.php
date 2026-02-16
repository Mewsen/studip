<?php
namespace Studip\LTI13a;

final class RoleMapper
{
    // Global
    const LTI_SYSTEM_ADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator';
    const LTI_SYSTEM_USER = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#User';

    // Institution
    const LTI_INSTITUTION_ADMIN = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator';


    // Course
    const LTI_COURSE_ADMIN      = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Administrator';
    const LTI_COURSE_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor';
    const LTI_COURSE_TA         = 'http://purl.imsglobal.org/vocab/lis/v2/membership#TeachingAssistant';
    const LTI_COURSE_MENTOR         = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Mentor';
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
                self::LTI_COURSE_TA,
                self::LTI_COURSE_MENTOR
            ],
            'tutor' => [
                self::LTI_COURSE_TA,
                self::LTI_COURSE_MENTOR
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
}
