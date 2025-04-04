<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case Card = 'card';
    case Cash = 'cash';
    case Transfer = 'transfer';
    case DirectDebit = 'direct_debit';
    case Check = 'check';
    case GiftCard = 'gift_card';

    public function label(): string
    {
        return match ($this) {
            self::Card => 'Carte bancaire',
            self::Cash => 'Espèces',
            self::Transfer => 'Virement',
            self::DirectDebit => 'Prélèvement automatique',
            self::Check => 'Chèque',
            self::GiftCard => 'Carte cadeau',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Card => '💳',
            self::Cash => '💵',
            self::Transfer => '🏦',
            self::DirectDebit => '🔁',
            self::Check => '📝',
            self::GiftCard => '🎁',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji().'&nbsp;&nbsp;'.$this->label();
    }

    public function color(): string
    {
        return match ($this) {
            self::Card => 'blue',
            self::Cash => 'green',
            self::Transfer => 'purple',
            self::DirectDebit => 'indigo',
            self::Check => 'gray',
            self::GiftCard => 'pink',
        };
    }

    public static function getMethodOptions(): array
    {
        return [
            self::Card->value => self::Card->label(),
            self::Cash->value => self::Cash->label(),
            self::Transfer->value => self::Transfer->label(),
            self::DirectDebit->value => self::DirectDebit->label(),
            self::Check->value => self::Check->label(),
            self::GiftCard->value => self::GiftCard->label(),
        ];
    }

    public static function getMethodOptionsWithEmojis(): array
    {
        return [
            self::Card->value => self::Card->labelWithEmoji(),
            self::Cash->value => self::Cash->labelWithEmoji(),
            self::Transfer->value => self::Transfer->labelWithEmoji(),
            self::DirectDebit->value => self::DirectDebit->labelWithEmoji(),
            self::Check->value => self::Check->labelWithEmoji(),
            self::GiftCard->value => self::GiftCard->labelWithEmoji(),
        ];
    }
}
