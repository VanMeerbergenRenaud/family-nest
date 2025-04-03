<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case Card = 'card';
    case Cash = 'cash';
    case Transfer = 'transfer';
    case DirectDebit = 'direct_debit';
    case Check = 'check';
    case Cryptocurrency = 'cryptocurrency';
    case GiftCard = 'gift_card';

    public function label(): string
    {
        return match ($this) {
            static::Card => 'Carte bancaire',
            static::Cash => 'Espèces',
            static::Transfer => 'Virement',
            static::DirectDebit => 'Prélèvement automatique',
            static::Check => 'Chèque',
            static::Cryptocurrency => 'Cryptomonnaie',
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
            static::Cryptocurrency => '🪙',
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
            static::Cryptocurrency => 'yellow',
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
            static::Cryptocurrency->value => static::Cryptocurrency->label(),
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
            static::Cryptocurrency->value => static::Cryptocurrency->labelWithEmoji(),
            static::GiftCard->value => static::GiftCard->labelWithEmoji(),
        ];
    }
}
