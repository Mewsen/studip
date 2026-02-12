<?php
namespace Lti;

use Range;
use Keyring;
use SimpleORMap;
use Ramsey\Uuid\Uuid;
use SimpleORMapCollection;
use Studip\Lti\Enum\RegistrationStatus;
use Studip\Lti\Enum\ResourceLaunchContainer;
use Studip\Lti\LTI1p3\RegistrationRepository;
use OAT\Library\Lti1p3Core\Security\Key\Key;
use OAT\Library\Lti1p3Core\Security\Key\KeyChain;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;

/**
 * @property int $id
 * @property string $name
 * @property string $status
 * @property string $version
 * @property int $mkdate
 * @property int $chdate
 * @property array $config_values
 * @property ?string $jwks_url
 * @property ?KeyChainInterface $keyChain
 * @property Range $range
 * @property SimpleORMapCollection<RegistrationConfig> $configs
 * @property SimpleORMapCollection<Deployment> $deployments
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

        $config['additional_fields']['jwks_url']['get'] = 'getJwksURL';
        $config['additional_fields']['keyChain']['get'] = 'getKeyChain';
        $config['additional_fields']['config_values']['get'] = 'getConfigValues';

        parent::configure($config);
    }

    public static function findTool(int $id): ?self
    {
        return self::findOneBySQL("`id` = ? AND `role`='tool'", [$id]);
    }

    public function toLti1p3Registration(?Deployment $deployment = null): RegistrationInterface
    {
        return new RegistrationRepository($this, $deployment);
    }

    public function getDefaultDeployment(): Deployment
    {
        return Deployment::firstOrCreate(
            [
                'is_default' => 1,
                'registration_id' => $this->id
            ],
            [
                'name' => _('Standard-Deployment'),
                'deployment_key' => bin2hex(random_bytes(6)),
                'client_id' => Uuid::uuid4()->toString()
            ]
        );
    }

    public function getJwksURL(): ?string
    {
        return $this->getConfigValues()['jwks_url'] ?? null;
    }

    public function getKeyChain(): ?KeyChainInterface
    {
        $publicKey = $this->getConfigValues()['public_key'];
        if (!$publicKey) {
            return null;
        }

        return new KeyChain(
            $this->id,
            $this->name,
            new Key($publicKey),
            null
        );
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

    public function transformData($with = []): array
    {
        $base = [
            ...$this->toRawArray(),
            'status' => RegistrationStatus::get($this->status),
            'range_name' => $this->range?->getFullName() ?? _('Global'),
            'mkdate' => date('c', $this->mkdate),
            'chdate' => date('c', $this->chdate),
            ...$this->getConfigValues(true)
        ];

        if (in_array('deployments', $with)) {
            $base['deployments'] = $this->deployments->transformData();
        }

        return $base;
    }

    public static function all(): array
    {
        return self::findBySQL("TRUE");
    }
}
