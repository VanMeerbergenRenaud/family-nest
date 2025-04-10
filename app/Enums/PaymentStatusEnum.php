<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum PaymentStatusEnum: string
{
    use EnumLabelTrait;

    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Late = 'late';
    case PartiallyPaid = 'partially_paid';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case Disputed = 'disputed';

    public function label(): string
    {
        return match ($this) {
            self::Paid => 'Payée',
            self::Unpaid => 'Non payée',
            self::Late => 'En retard',
            self::PartiallyPaid => 'Partiellement payée',
            self::Cancelled => 'Annulée',
            self::Refunded => 'Remboursée',
            self::Disputed => 'Contestée',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Paid => '✅',
            self::Unpaid => '⏳',
            self::Late => '⏰',
            self::PartiallyPaid => '💸',
            self::Cancelled => '❌',
            self::Refunded => '️🏦',
            self::Disputed => '⚖️',

        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Paid => 'green',
            self::Unpaid => 'gray',
            self::Late => 'red',
            self::PartiallyPaid => 'yellow',
            self::Cancelled => 'slate',
            self::Refunded => 'purple',
            self::Disputed => 'orange',
        };
    }

    public static function getStatusOptions(): array
    {
        return self::getOptions();
    }

    public static function getStatusOptionsWithEmojis(): array
    {
        return self::getOptionsWithEmojis();
    }
}
