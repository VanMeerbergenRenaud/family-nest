<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum PaymentMethodEnum: string
{
    use EnumLabelTrait;

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
            self::DirectDebit => '🔄',
            self::Check => '📝',
            self::GiftCard => '🎁',
        };
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
        return self::getOptions();
    }

    public static function getMethodOptionsWithEmojis(): array
    {
        return self::getOptionsWithEmojis();
    }
}
