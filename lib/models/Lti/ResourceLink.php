<?php
namespace Lti;

use Course;
use DBManager;
use JSONArrayObject;
use SimpleORMap;

class ResourceLink extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_resource_links';

        $config['serialized_fields']['options'] = JSONArrayObject::class;

        $config['belongs_to']['course'] = [
            'class_name'  => Course::class,
            'foreign_key' => 'course_id'
        ];
        $config['belongs_to']['deployment'] = [
            'class_name'  => Deployment::class,
            'foreign_key' => 'deployment_id'
        ];

        $config['registered_callbacks']['before_create'] = ['cbCalculatePosition'];

        parent::configure($config);
    }

    /**
     * Calculates the position for a new LTI resource link in the course.
     */
    public function cbCalculatePosition() : void
    {
        $this->position = self::countByCourse_id($this->course_id);
    }

    public function delete()
    {
        $course_id = $this->course_id;
        $position = $this->position;
        if ($result = parent::delete()) {
            DBManager::get()->execute(
                "UPDATE `lti_resource_links`
                 SET `position` = position - 1
                 WHERE `course_id` = :course_id AND `position` > :position",
                [
                    'course_id' => $course_id,
                    'position'  => $position
                ]
            );
        }

        return $result;
    }

    public function transformData($with = []): array
    {
        $base = [
            ...$this->toRawArray(),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate)
        ];

        if (in_array('deployment', $with)) {
            $base['deployment'] = $this->deployment->transformData();
        }

        if (in_array('registration', $with)) {
            $base['registration'] = Registration::find($this->deployment->registration_id)->transformData();
        }

        return $base;
    }
}
