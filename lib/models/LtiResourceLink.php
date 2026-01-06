<?php
/*
 * LtiResourceLink.php
 * This file is part of Stud.IP.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */


use Lti\Deployment;
use OAT\Library\Lti1p3Core\Resource\LtiResourceLink\LtiResourceLinkInterface;
use OAT\Library\Lti1p3Core\Util\Collection\Collection;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;

/**
 * The LtiResourceLink class is a model for the lti_resource_links table.
 *
 * @property int $id database column
 * @property int $deployment_id database column
 * @property string $course_id database column
 * @property string $title database column
 * @property string $description database column
 * @property int $position database column
 * @property string $launch_url database column
 * @property JSONArrayObject|null $options database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property ?Deployment $deployment related object
 * @property ?Course $course related object
 */
class LtiResourceLink extends SimpleORMap implements LtiResourceLinkInterface
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
            'class_name'        => LtiGrade::class,
            'assoc_foreign_key' => 'link_id',
            'on_delete'         => 'delete'
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

    /**
     * Delete this entity.
     */
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

    /**
     * Find a single entry by course_id and position.
     *
     * @return static|null
     */
    public static function findByCourseAndPosition($course_id, $position)
    {
        return self::findOneBySQL('course_id = ? AND position = ?', [$course_id, $position]);
    }

    public function getLaunchURL()
    {
        $registration = $this->deployment->registration;
        $registrationConfigs = $registration->getConfigValues();

        if (!empty($registration) && empty($registrationConfigs['allow_custom_url']) && empty($registrationConfigs['deep_linking']) || empty($registrationConfigs['launch_url'])) {
            return $registrationConfigs['launch_url'];
        }
        return $registrationConfigs['launch_url'];
    }

    //OAT library LtiResourceLinkInterface and ResourceInterface implementation:

    public function getUrl(): ?string
    {
        return $this->getLaunchURL();
    }

    public function getIcon(): ?array
    {
        return null;
    }

    public function getThumbnail(): ?array
    {
        if ($this->course) {
            return [$this->course->getItemAvatarURL()];
        }
        return null;
    }

    public function getIframe(): ?array
    {
        //Not supported.
        return null;
    }

    public function getCustom(): ?array
    {
        //Not supported.
        return null;
    }

    public function getLineItem(): ?array
    {
        // TODO: Implement getLineItem() method.
        return null;
    }

    public function getAvailability(): ?array
    {
        // TODO: Implement getAvailability() method.
        return null;
    }

    public function getSubmission(): ?array
    {
        // TODO: Implement getSubmission() method.
        return null;
    }

    public function getIdentifier(): string
    {
        return strval($this->id);
    }

    public function getType(): string
    {
        return 'ltiResourceLink';
    }

    public function getTitle(): ?string
    {
        return $this->title ?? $this->deployment->registration->name ?? null;
    }

    public function getText(): ?string
    {
        return null;
    }

    public function getProperties(): CollectionInterface
    {
        $collection = new Collection();
        $collection->add([
            'url' => $this->getUrl(),
            'title' => $this->getTitle()
        ]);
        return $collection;
    }

    public function normalize(): array
    {
        return array_filter(
            array_merge(
                $this->getProperties()->all(),
                ['type' => $this->getType()]
            )
        );
    }

    public function getCustomParameters(): string
    {
        if (!empty($this->custom_parameters)) {
            return $this->custom_parameters;
        }

        return $this->deployment->registration->getConfigValues()['custom_parameters'];
    }

    public function getCustomLtiParameterArray() : array
    {
        $parameterStr = $this->getCustomParameters();
        if (empty($parameterStr)) {
            return [];
        }
        $parameters = explode("\n", $parameterStr);
        $array = [];
        foreach ($parameters as $parameter) {
            $key_value_parts = explode('=', $parameter, 2);
            if (count($key_value_parts) === 2) {
                $array[trim($key_value_parts[0])] = trim($key_value_parts[1]);
            }
        }
        return ['https://purl.imsglobal.org/spec/lti/claim/custom' => $array];
    }
}
