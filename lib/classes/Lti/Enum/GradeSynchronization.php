<?php
namespace Studip\Lti\Enum;

enum GradeSynchronization: int
{
    case BasicOutcome = 0;
    case GradeSyncOnly = 1;
    case GradeManagement = 2;

    public static function all(): array
    {
        return [
            self::BasicOutcome->value => [
                'value' => self::BasicOutcome->value,
                'label' => _('Basic-Outcome')
            ],
            self::GradeSyncOnly->value => [
                'value' => self::GradeSyncOnly->value,
                'label' => _('Nur Grade-Synchronisierung')
            ],
            self::GradeManagement->value => [
                'value' => self::GradeManagement->value,
                'label' => _('Grade-Synchronisierung und verwaltung')
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
            'value' => self::BasicOutcome->value,
            'label' => _('Basic-Outcome')
        ];
    }
}
