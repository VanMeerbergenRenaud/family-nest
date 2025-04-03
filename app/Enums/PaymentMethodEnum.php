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
            static::Card => 'Carte bancaire',
            static::Cash => 'Espèces',
            static::Transfer => 'Virement',
            static::DirectDebit => 'Prélèvement automatique',
            static::Check => 'Chèque',
            static::GiftCard => 'Carte cadeau',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            static::Card => '💳',
            static::Cash => '💵',
            static::Transfer => '🏦',
            static::DirectDebit => '🔁',
            static::Check => '📝',
            static::GiftCard => '🎁',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji() . '&nbsp;&nbsp;' . $this->label();
    }

    public function color(): string
    {
        return match ($this) {
            static::Card => 'blue',
            static::Cash => 'green',
            static::Transfer => 'purple',
            static::DirectDebit => 'indigo',
            static::Check => 'gray',
            static::GiftCard => 'pink',
        };
    }

    public static function getMethodOptions(): array
    {
        return [
            static::Card->value => static::Card->label(),
            static::Cash->value => static::Cash->label(),
            static::Transfer->value => static::Transfer->label(),
            static::DirectDebit->value => static::DirectDebit->label(),
            static::Check->value => static::Check->label(),
            static::GiftCard->value => static::GiftCard->label(),
        ];
    }

    public static function getMethodOptionsWithEmojis(): array
    {
        return [
            static::Card->value => static::Card->labelWithEmoji(),
            static::Cash->value => static::Cash->labelWithEmoji(),
            static::Transfer->value => static::Transfer->labelWithEmoji(),
            static::DirectDebit->value => static::DirectDebit->labelWithEmoji(),
            static::Check->value => static::Check->labelWithEmoji(),
            static::GiftCard->value => static::GiftCard->labelWithEmoji(),
        ];
    }
}
