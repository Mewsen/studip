<?php
namespace Studip\Lti\Enum;

enum UserIdentityMappingContext: string {
    case DeepLink = 'deep_linking';
    case ResourceLink = 'resource_link';

    public static function all(): array
    {
        return [
            self::DeepLink->value => [
                'value' => self::DeepLink->value,
                'label' => _('LTI-Deeplink')
            ],
            self::ResourceLink->value => [
                'value' => self::ResourceLink->value,
                'label' => _('LTI-Ressource')
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
            'value' => self::ResourceLink->value,
            'label' => _('LTI-Ressource')
        ];
    }
}
