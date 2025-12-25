<?php
namespace Lti;

use Keyring;
use Lti\Enum\ResourceLaunchContainer;
use Range;
use SimpleORMap;
use Studip\LTI13a\RegistrationRepository;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;

/**
 * @property int $id
 * @property bool $state
 */
class Registration extends SimpleORMap
{
    protected static function configure($config = []): void
    {
        $config['db_table'] = 'lti_registrations';

        $config['has_many']['configs'] = [
            'class_name' => RegistrationConfig::class,
            'assoc_foreign_key' => 'registration_id'
        ];

        $config['has_many']['deployments'] = [
            'class_name' => Deployment::class,
            'assoc_foreign_key' => 'registration_id',
            'order_by' => 'ORDER BY mkdate DESC',
        ];

        $config['additional_fields']['range'] = [
            'set' => function (Registration $registration, string $field, Range | string | null $range) {
                if ($range instanceof Range) {
                    $registration->range_id = $range->getRangeId();
                } else {
                    $registration->range_id = 'global';
                }
            },
            'get' => function (Registration $registration): ?Range {
                return get_object_by_range_id($registration->range_id);
            },
        ];

        $config['additional_fields']['keyring']['get'] = 'getKeyring';
        $config['additional_fields']['config_values']['get'] = 'getConfigValues';

        parent::configure($config);
    }

    public static function findTool(int $id): ?self
    {
        return self::findOneBySQL("`id` = ? AND `role`='role'", [$id]);
    }

//    public function __get($field)
//    {
//        $configValues = $this->getConfigValues();
//        if (array_key_exists($field, $configValues)) {
//            return $configValues[$field];
//        }
//
//        return parent::__get($field);
//    }

    public function toLti1p3Registration(Deployment $deployment = null): RegistrationInterface
    {
        return new RegistrationRepository($this, $deployment);
    }

    public function getDefaultDeployment(): ?Deployment
    {
        return Deployment::findOneBySQL("registration_id = ? ORDER BY id", [$this->id]);
    }

    public function getKeyring(): ?Keyring
    {
        return Keyring::findOneBySQL(
            "`range_type` = 'lti_registration' AND `range_id` = ?",
            [$this->id]
        );
    }

    public function getConfigValues(): array
    {
        $configValues = [];
        foreach (parent::__get('configs') as $config) {
            $configValues[strtolower($config->name)] = $config->value;
        }

        return $configValues;
    }

    /**
     * Checks whether auth user may have the permissions to edit the registration.
     * @return bool
     */
    public function canAuthEdit(): bool
    {
        return $this->range_id === 'global' && $GLOBALS['perm']->have_perm('root')
            || ($this->range_id !== 'global' && $GLOBALS['perm']->have_studip_perm('tutor', $this->range_id));
    }

    public function transformData($with = []): array
    {
        $configs = $this->getConfigValues();

        $base = [
            ...$this->toRawArray(),
            'state' => (bool) $this->state,
            'range_name' => $this->range?->getFullName() ?? _('Global'),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate),
            ...$configs,
            'container' => ResourceLaunchContainer::get($configs['launch_container']),
        ];

        if (in_array('deployments', $with)) {
            $base['deployments'] = $this->deployments->transformData();
        }

        return $base;
    }

    public static function all(): array
    {
        return static::findBySQL("TRUE");
    }
}
