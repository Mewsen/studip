<?php
namespace Lti;

use User;
use SimpleORMap;

/**
 * NOTE: LtiGrade is only for the LTI 1.0/1.1 interface.
 * The LTI 1.3A interface uses the grade book tables for storing grades.
 */

class Grade extends SimpleORMap
{
    protected static function configure($config = []): void
    {
        $config['db_table'] = 'lti_grade';

        // TODO:: rename link and link_id to deployment and deployment_id
        $config['belongs_to']['link'] = [
            'class_name' => Deployment::class,
            'foreign_key' => 'link_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }
}
