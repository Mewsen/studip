<?php
namespace Studip\Lti\Enum;

use Lti\Publication;
use Lti\Registration;
use Lti\ResourceLink;

enum ConfigurableType: string {
    case Registration = Registration::class;
    case Publication = Publication::class;
    case ResourceLink = ResourceLink::class;

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
