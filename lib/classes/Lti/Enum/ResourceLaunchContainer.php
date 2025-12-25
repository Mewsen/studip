<?php
namespace Lti\Enum;

enum ResourceLaunchContainer: string {
    case NewWindow = '1';
    case Iframe = '2';

    public static function all(): array
    {
        return [
            self::NewWindow->value => [
                'value' => (int) self::NewWindow->value,
                'label' => _('Neues Fenster')
            ],
            self::Iframe->value => [
                'value' => (int) self::Iframe->value,
                'label' => _('Iframe')
            ]
        ];
    }

    public static  function get(int $value)
    {
        return static::all()[(string) $value] ?? static::default();
    }

    public static function default(): array {
        return [
            'value' => (int) self::NewWindow->value,
            'label' => _('Neues Fenster')
        ];
    }
}
