<?php
namespace Lti;

use Course;
use DBManager;
use SimpleORMap;
use JSONArrayObject;
use SimpleORMapCollection;
use Studip\Lti\Enum\ResourceLaunchContainer;
use Studip\Lti\LTI1p3\ResourceLinkRepository;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $position
 * @property string $custom_parameters
 * @property JSONArrayObject $options
 * @property int $mkdate
 * @property int $chdate
 * @property Course $course
 * @property Deployment $deployment
 * @property Registration $registration
 * @property SimpleORMapCollection<Config> $configs
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

        $config['has_many']['configs'] = [
            'class_name' => Config::class,
            'assoc_foreign_key' => 'configurable_id'
        ];

        $config['additional_fields']['config_values']['get'] = 'getConfigValues';

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
            'mkdate' => date('c', $this->mkdate),
            'chdate' => date('c', $this->chdate),
            ...$this->getConfigValues(true)
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

    public function getConfigValues(bool $typeCasting = false): array
    {
        return collect(parent::__get('configs'))->mapWithKeys(function ($config) use ($typeCasting) {
            $key = strtolower($config->name);
            $value = $config->value;

            if ($typeCasting && $key === 'launch_container') {
                return [
                    $key => $value,
                    'container' => ResourceLaunchContainer::get($value ?? 'window')
                ];
            }

            return [$key => $value];
        })->toArray();
    }
}
