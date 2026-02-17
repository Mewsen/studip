<?php
namespace Studip\Lti\Enum;

trait StatusHelpers {

    public static function all(): array
    {
        return [
            self::Active->value => [
                'value' => self::Active->value,
                'label' => _('Aktiv')
            ],
            self::Inactive->value => [
                'value' => self::Inactive->value,
                'label' => _('Inaktiv')
            ]
        ];
    }

    public static function get(string $value): array
    {
        return static::all()[$value] ?? static::default();
    }

    public static function fromBoolean(bool $value): string
    {
        return $value ? self::Active->value : self::Inactive->value;
    }

    public static function default(): array
    {
        return [
            'value' => self::Inactive->value,
            'label' => _('Inaktiv')
        ];
    }

}
