<?php
namespace Lti\Enum;

enum ResourceLaunchContainer: string {
    case Window = 'window';
    case Iframe = 'iframe';

    public static function all(): array
    {
        return [
            self::Window->value => [
                'value' => self::Window->value,
                'label' => _('Neues Fenster')
            ],
            self::Iframe->value => [
                'value' => self::Iframe->value,
                'label' => _('Iframe')
            ]
        ];
    }

    public static  function get(string $value)
    {
        return static::all()[$value] ?? static::default();
    }

    public static function default(): array {
        return [
            'value' => (int) self::Window->value,
            'label' => _('Neues Fenster')
        ];
    }
}
