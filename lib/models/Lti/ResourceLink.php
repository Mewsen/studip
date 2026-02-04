<?php
namespace Lti;

use Course;
use DBManager;
use JSONArrayObject;
use Studip\Lti\Enum\ResourceLaunchContainer;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;
use SimpleORMap;
use Studip\Lti\LTI1p3\ResourceLinkRepository;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $position
 * @property string $icon
 * @property string $color
 * @property string $launch_container
 * @property string $custom_parameters
 * @property JSONArrayObject $options
 * @property int $mkdate
 * @property int $chdate
 * @property Course $course
 * @property Deployment $deployment
 * @property Registration $registration
 */
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

        $config['has_many']['grades'] = [
            'class_name' => Grade::class,
            'assoc_foreign_key' => 'link_id',
            'on_delete' => 'delete'
        ];

        $config['additional_fields']['registration']['get'] = 'getRegistration';

        $config['registered_callbacks']['before_create'] = ['cbCalculatePosition'];

        parent::configure($config);
    }

    /**
     * Calculates the position for a new LTI resource link in the course.
     */
    public function cbCalculatePosition(): void
    {
        $this->position = self::countByCourse_id($this->course_id);
    }

    public function delete(): bool
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
            'container' => ResourceLaunchContainer::get($this->launch_container),
            'mkdate' => date('c', $this->mkdate),
            'chdate' => date('c', $this->chdate)
        ];

        if (in_array('deployment', $with)) {
            $base['deployment'] = $this->deployment->transformData();
        }

        if (in_array('registration', $with)) {
            $base['registration'] = Registration::find($this->deployment->registration_id)->transformData();
        }

        return $base;
    }

    public function getLaunchURL(): string
    {
        $registration = $this->deployment->registration;
        $registrationConfigs = $registration->getConfigValues();

        if (empty($registrationConfigs['allow_custom_url']) && empty($registrationConfigs['deep_linking']) || empty($registrationConfigs['launch_url'])) {
            return $registrationConfigs['launch_url'];
        }
        return $registrationConfigs['launch_url'];
    }

    public function getCustomParameters(): ?string
    {
        if (!empty($this->custom_parameters)) {
            return $this->custom_parameters;
        }

        return $this->deployment->registration->getConfigValues()['custom_parameters'] ?? null;
    }

    public function toLti1p3ResourceLink(): LtiResourceLinkInterface
    {
        return new ResourceLinkRepository($this);
    }

    public function getRegistration(): Registration
    {
        return $this->deployment->registration;
    }
}
