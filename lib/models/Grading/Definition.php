<?php

namespace Grading;

use OAT\Library\Lti1p3Ags\Model\LineItem\LineItem;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemInterface;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemSubmissionReview;

/**
 * @license GPL2 or any later version
 *
 * @property int $id database column
 * @property string $course_id database column
 * @property string $item database column
 * @property string $name database column
 * @property string $tool database column
 * @property string $category database column
 * @property int $position database column
 * @property float $weight database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \SimpleORMapCollection<Instance> $instances has_many Instance
 * @property \Course $course belongs_to \Course
 */
class Definition extends \SimpleORMap
{
    const CUSTOM_DEFINITIONS_CATEGORY = 'xyzzy';

    protected static function configure($config = [])
    {
        $config['db_table'] = 'grading_definitions';

        $config['belongs_to']['course'] = [
            'class_name' => \Course::class,
            'foreign_key' => 'course_id',
        ];
        $config['has_many']['instances'] = [
            'class_name' => Instance::class,
            'assoc_foreign_key' => 'definition_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
        ];

        parent::configure($config);
    }

    public static function getCategoriesByCourse(\Course $course)
    {
        $query = 'SELECT category FROM grading_definitions
                  WHERE course_id = ?
                  GROUP BY category
                  ORDER BY category ASC';

        $stmt = \DBManager::get()->prepare($query);
        $stmt->execute([$course->id]);

        $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $customIndex = array_search(self::CUSTOM_DEFINITIONS_CATEGORY, $categories);
        if (false !== $customIndex) {
            unset($categories[$customIndex]);
            array_unshift($categories, self::CUSTOM_DEFINITIONS_CATEGORY);
        }

        return $categories;
    }

    public static function findByCourse(\Course $course)
    {
        return Definition::findBySQL('course_id = ? ORDER BY position ASC, name ASC', [$course->id]);
    }

    public function toLineItem() : LineItemInterface
    {
        $resource_link_identifier = $this->tool ?? '';
        $deployment_id = '';
        if ($resource_link_identifier) {
            $lti_resource_link = \LtiResourceLink::find($resource_link_identifier);
            if ($lti_resource_link) {
                $deployment_id = $lti_resource_link->deployment_id;
            }
        }

        $identifier = \URLHelper::getURL(sprintf(
            'dispatch.php/lti/ags/line_item/%1$s/%2$s',
            $resource_link_identifier,
            $this->id
        ));

        return new LineItem(
            PHP_FLOAT_MAX,
            $this->name,
            $identifier,
            $deployment_id,
            $resource_link_identifier
        );
    }
}
