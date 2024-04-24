<?php
/**
 * LtiDeployment.php - A class that represents an LTI tool deployment.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @author      Moritz Strohm
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 *
 * @property int $id database column
 * @property int $position database column
 * @property string $course_id database column
 * @property string $title database column
 * @property string $description database column
 * @property int $tool_id database column
 * @property string $launch_url database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property JSONArrayObject|null $options database column
 * @property SimpleORMapCollection|LtiGrade[] $grades has_many LtiGrade
 * @property Course $course belongs_to Course
 * @property LtiTool $tool belongs_to LtiTool
 */

class LtiDeployment extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_deployments';

        $config['serialized_fields']['options'] = JSONArrayObject::class;

        $config['belongs_to']['course'] = [
            'class_name'  => Course::class,
            'foreign_key' => 'course_id'
        ];
        $config['belongs_to']['tool'] = [
            'class_name'  => LtiTool::class,
            'foreign_key' => 'tool_id'
        ];

        $config['has_many']['grades'] = [
            'class_name'        => LtiGrade::class,
            'assoc_foreign_key' => 'link_id',
            'on_delete'         => 'delete'
        ];

        parent::configure($config);
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

    /**
     * Delete this entity.
     */
    public function delete()
    {
        $db = DBManager::get();
        $course_id = $this->course_id;
        $position = $this->position;

        if ($result = parent::delete()) {
            $db->execute('UPDATE `lti_deployments` SET `position` = position - 1 WHERE `course_id` = ? AND `position` > ?', [$course_id, $position]);
        }

        return $result;
    }

    public function getToolLtiVersion() : string
    {
        return $this->tool->lti_version ?? '';
    }


    /**
     * Get the launch_url of this entry.
     */
    public function getLaunchURL()
    {
        if (empty($this->tool->allow_custom_url) && empty($this->tool->deep_linking) || empty($this->launch_url)) {
            return $this->tool->launch_url ?? '';
        }
        return '';
    }

    /**
     * Get the consumer_key of this entry.
     */
    public function getConsumerKey()
    {
        return $this->tool->consumer_key ?? '';
    }

    /**
     * Get the consumer_secret of this entry.
     */
    public function getConsumerSecret()
    {
        return $this->tool->consumer_secret ?? '';
    }

    /**
     * Get the oauth_signature_method of this entry.
     */
    public function getOauthSignatureMethod()
    {
        return $this->tool->oauth_signature_method ?? 'sha1';
    }

    /**
     * Get the custom_parameters of this entry.
     */
    public function getCustomParameters()
    {
        return $this->tool->custom_parameters . "\n" . $this->options['custom_parameters'] ?? '';
    }

    public function getCustomLtiParameterArray() : array
    {
        $parameters = explode('\n', $this->getCustomParameters());
        $array = [];
        foreach ($parameters as $parameter) {
            $key_value_parts = explode('=', $parameter, 2);
            if (count($key_value_parts) === 2) {
                $array[trim($key_value_parts[0])] = trim($key_value_parts[1]);
            }
        }
        return ['https://purl.imsglobal.org/spec/lti/claim/custom' => $array];
    }

    /**
     * Get the send_lis_person attribute of this entry.
     */
    public function getSendLisPerson()
    {
        return $this->tool->send_lis_person;
    }

    /**
     * Whether the LtiData instance uses its own (private) tool
     * or one of the globally defined LTI tools.
     *
     * @return bool True, if the LtiData instance uses its own tool, false otherwise.
     */
    public function hasOwnTool() : bool
    {
        return $this->tool && !$this->tool->is_global;
    }
}
