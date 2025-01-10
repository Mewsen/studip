<?php

/**
 * @license GPL2 or any later version
 *
 * @property string $id alias column for tag_hash
 * @property string $name database column
 * @property int $active database column
 * @property int $chdate database column
 * @property int $mkdate database column
 *
 */
class Tag extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'tags';
        $config['has_many']['related_objects'] = [
            'class_name' => TagRelation::class,
            'assoc_foreign_key' => 'tag_id',
            'order_by' => 'ORDER BY `range_type` ASC, `range_id` ASC',
            'on_store' => 'store',
            'on_delete' => 'delete'
        ];
        parent::configure($config);
    }

    public static function isActive($tag_name)
    {
        $tag_name = self::normalizeName($tag_name);
        $tag = static::findOneByName($tag_name);
        return $tag === false || $tag['active'] > 0;
    }

    public static function getByRange($range_id, $range_type)
    {
        return Tag::findBySQL('INNER JOIN `tags_relations` ON (`tags_relations`.`tag_id` = `tags`.`id`)
            WHERE `tags_relations`.`range_id` = :range_id
                AND `tags_relations`.`range_type` = :range_type AND `tags`.`active` = 1 ORDER BY `tags`.`name` ASC', [
                    'range_id' => $range_id,
                    'range_type' => $range_type
        ]);
    }

    public static function normalizeName($name)
    {
        $name = mb_strtolower($name);
        $name = str_replace(
            [' ', "\n", '|', '#'],
            ['-', '-',  '-', ''],
            $name
        );
        return $name;
    }
}
