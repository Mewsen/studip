<?php
namespace Forum\Enum;

enum SubscriptionNotificationType: string {
    case All = 'all';
    case RepliesOnly = 'replies_only';
    case None = 'none';

    public static function getTypes(): array {
        return [
            self::All->value => [
                'value' => self::All->value,
                'label' => _('Alle')
            ],
            self::RepliesOnly->value => [
                'value' => self::RepliesOnly->value,
                'label' => _('Nur Antworten')
            ],
            self::None->value => [
                'value' => self::None->value,
                'label' => _('Keine')
            ]
        ];
    }
}
