<?php
namespace Lti;

use Keyring;
use SimpleORMap;
use stdClass;
use Studip\LTI13a\RegistrationRepository;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use User;

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
            'assoc_foreign_key' => 'registration_id'
        ];

        $config['additional_fields']['keyring']['get'] = 'getKeyring';
        $config['additional_fields']['config_values']['get'] = 'getConfigValues';

        parent::configure($config);
    }

    public static function findTool(int $id): ?self
    {
        return self::findOneBySQL("`id` = ? AND `role`='role'", [$id]);
    }

    public function __get($field)
    {
        if ($field !== 'configs') {
            return parent::__get($field);
        }

        // Build a plain object with config name/value pairs
        $configSource = new stdClass();
        foreach (parent::__get('configs') as $config) {
            $key = $config->name ?? null;

            if ($key !== null) {
                $configSource->{$key} = $config->value ?? null;
            }
        }

        return new class($configSource) {
            private $data;

            public function __construct(stdClass $data)
            {
                $this->data = $data;
            }

            public function __get($prop)
            {
                return $this->data->{$prop} ?? null;
            }

            public function __isset($prop)
            {
                return isset($this->data->{$prop});
            }
        };
    }

    public function getLti1p3Registration(): RegistrationInterface
    {
        return new RegistrationRepository($this);
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

    public static function updateOrCreate(array $attributes, array $values = []): self
    {
        $whereClauses = [];
        foreach ($attributes as $key => $value) {
            $whereClauses[] = "$key = :$key";
        }

        $record = static::findOneBySQL(implode(' AND ', $whereClauses), $attributes);

        if ($record) {
            $record->setData($values);
            $record->store();
            return $record;
        }

        return static::create(array_merge($attributes, $values));
    }

    /**
     * Checks whether a user may have the permissions to edit the tool.
     *
     * @param string $user_id The ID of the user whose edit permissions shall be checked.
     *
     * @return bool True, if the user may edit the tool, false otherwise.
     */
    public function isEditableByUser(string $user_id = null) : bool
    {
        $user_id ??= User::findCurrent()->id;
        return $this->range_id === 'global' && $GLOBALS['perm']->have_perm('root')
            || ($this->range_id !== 'global' && $GLOBALS['perm']->have_studip_perm('tutor', $this->range_id));
    }
}
