<?php
namespace Studip\Lti\Enum;

enum UserProvisioningMode: int
{
    case NewAccountsOnly = 1;
    case ExistingAndNewAccounts = 2;
    case ExistingAccountsOnly = 3;

    public static function all(): array
    {
        return [
            strval(self::NewAccountsOnly->value) => [
                'value' => self::NewAccountsOnly->value,
                'label' => _('Nur neue Konten (automatisch)')
            ],
            strval(self::ExistingAndNewAccounts->value) => [
                'value' => self::ExistingAndNewAccounts->value,
                'label' => _('Bestehende und neue Konten (Abfrage)')
            ],
            strval(self::ExistingAccountsOnly->value) => [
                'value' => self::ExistingAccountsOnly->value,
                'label' => _('Nur bestehende Konten (Abfrage)')
            ]
        ];
    }

    public static function get(int $value): array
    {
        return self::all()[strval($value)] ?? self::default();
    }

    public static function default(string $role = 'autor'): array
    {
        return match ($role) {
            'dozent' => [
                'value' => self::ExistingAndNewAccounts->value,
                'label' => _('Bestehende und neue Konten (Abfrage)')
            ],
            default => [
                'value' => self::NewAccountsOnly->value,
                'label' => _('Nur neue Konten (automatisch)')
            ]
        };
    }
}
