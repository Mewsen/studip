<?php
namespace Studip\Lti\Enum;

enum LtiVersion: string {
    case Lti1P1 = '1.1';
    case Lti1p3a = '1.3a';

    public static function all(): array
    {
        return [
            self::Lti1P1->value => [
                'value' => self::Lti1P1->value,
                'label' => _('1.1')
            ],
            self::Lti1p3a->value => [
                'value' => self::Lti1p3a->value,
                'label' => _('1.3a')
            ]
        ];
    }

    public static function get(string $value): array
    {
        return self::all()[$value] ?? self::default();
    }

    public static function default(): array
    {
        return [
            'value' => (int) self::Lti1p3a->value,
            'label' => _('1.3a')
        ];
    }
}
