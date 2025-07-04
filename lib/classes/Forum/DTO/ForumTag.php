<?php
namespace Forum\DTO;

use DBManager;

class ForumTag
{
    public function __construct(
        public string $id,
        public string $name
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['name'] ?? ''
        );
    }

    public function toRawArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    public static function getForumTags(): array
    {
        return DBManager::get()->fetchAll(
            "SELECT DISTINCT `tags_relations`.`tag_id`, `tags`.`name` FROM `tags`
                        LEFT JOIN  `tags_relations` ON `tags`.`id` = `tags_relations`.`tag_id`
                        WHERE `tags_relations`.`range_type` = 'forum' AND `tags`.`active` = TRUE
                        ORDER BY `tags`.`mkdate` DESC",
            [],
            function ($tag) {
                return self::fromArray([
                    'id' => $tag['tag_id'],
                    'name' => $tag['name']
                ]);
            }
        );
    }
}
