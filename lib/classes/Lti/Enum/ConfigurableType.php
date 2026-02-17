<?php
namespace Studip\Lti\Enum;

enum ConfigurableType: string {
    case Registration = 'registration';
    case Publication = 'publication';
    case ResourceLink = 'resource_link';

    public static function all(): array
    {
        return [
            self::Registration->value => [
                'value' => self::Registration->value,
                'label' => _('Registrierung')
            ],
            self::Publication->value => [
                'value' => self::Publication->value,
                'label' => _('Veröffentlichung')
            ],
            self::ResourceLink->value => [
                'value' => self::ResourceLink->value,
                'label' => _('Ressource')
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
            'value' => self::Registration->value,
            'label' => _('Registrierung')
        ];
    }
}
