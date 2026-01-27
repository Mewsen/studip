<?php
namespace Lti;

use Avatar;
use Lti\Enum\UserProvisioningMode;
use Range;
use SimpleORMap;
use Studip\Lti\Enum\PublicationStatus;
use User;
use SimpleORMapCollection;

/**
 * @property string $name
 * @property string $version
 * @property string $status
 * @property string $publication_key
 * @property array $config_values
 * @property Range $range
 * @property User $user
 * @property int $mkdate
 * @property int $chdate
 * @property SimpleORMapCollection<PublicationConfig> $configs
 * @property SimpleORMapCollection<User> $members
 */
class Publication extends SimpleORMap
{
    protected static function configure($config = []): void
    {
        $config['db_table'] = 'lti_publications';

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        $config['additional_fields']['range'] = [
            'set' => function (Publication $publication, string $field, Range | string | null $range) {
                if ($range instanceof Range) {
                    $publication->range_id = $range->getRangeId();
                }
            },
            'get' => function (Publication $publication): ?Range {
                return get_object_by_range_id($publication->range_id);
            },
        ];

        $config['has_many']['configs'] = [
            'class_name' => PublicationConfig::class,
            'assoc_foreign_key' => 'publication_id'
        ];

        $config['has_many']['members'] = [
            'class_name' => PublicationUser::class,
            'assoc_foreign_key' => 'publication_id'
        ];

        $config['additional_fields']['config_values']['get'] = 'getConfigValues';

        parent::configure($config);
    }

    public function getConfigValues(bool $typeCasting = false): array
    {
        $dateFields = ['start_date', 'end_date', 'enrollment_deadline'];
        $numberFields = ['maximum_enrolled_users'];

        return collect(parent::__get('configs'))->mapWithKeys(function ($config) use ($typeCasting, $dateFields, $numberFields) {
            $key = strtolower($config->name);
            $value = $config->value;

            if ($value && $typeCasting && in_array($key, $dateFields, true)) {
                $value = date('c', $value);
            } else if ($value && $typeCasting && in_array($key, $numberFields, true)) {
                $value = (int) $value;
            } else if (in_array($key, ['provisioning_mode_instructor', 'provisioning_mode_student']) && $typeCasting) {
                $value = UserProvisioningMode::get($value);
            }

            return [$key => $value];
        })->toArray();
    }

    public function transformData($with = []): array
    {
        $base = [
            ...$this->toRawArray(),
            'status' => PublicationStatus::get($this->status),
            'range_name' => $this->range?->getFullName(),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate),
            'custom_parameter' => 'id='. $this->publication_key,
            'user' => [
                'id'         => $this->user->id,
                'name'       => $this->user->getFullName(),
                'username'   => $this->user->username,
                'avatar_url' => Avatar::getAvatar($this->user->id)->getURL(Avatar::MEDIUM)
            ],
            ...$this->getConfigValues(true)
        ];

        if (in_array('members', $with)) {
            $base['members'] = $this->members->transformData();
        }

        return $base;
    }
}
