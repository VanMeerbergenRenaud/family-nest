<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
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
            static::Unpaid => 'Non payée',
            static::Paid => 'Payée',
            static::Late => 'En retard',
            static::PartiallyPaid => 'Partiellement payée',
            static::Cancelled => 'Annulée',
            static::Refunded => 'Remboursée',
            static::Disputed => 'Contestée',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            static::Unpaid => '⏳',
            static::Paid => '✅',
            static::Late => '⚠️',
            static::PartiallyPaid => '💰',
            static::Cancelled => '❌',
            static::Refunded => '↩️',
            static::Disputed => '⚖️',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji() . '&nbsp;&nbsp;' . $this->label();
    }

    public function color(): string
    {
        return match ($this) {
            static::Unpaid => 'gray',
            static::Paid => 'green',
            static::Late => 'red',
            static::PartiallyPaid => 'yellow',
            static::Cancelled => 'slate',
            static::Refunded => 'purple',
            static::Disputed => 'orange',
        };
    }

    public static function getStatusOptions(): array
    {
        return [
            static::Unpaid->value => static::Unpaid->label(),
            static::Paid->value => static::Paid->label(),
            static::Late->value => static::Late->label(),
            static::PartiallyPaid->value => static::PartiallyPaid->label(),
            static::Cancelled->value => static::Cancelled->label(),
            static::Refunded->value => static::Refunded->label(),
            static::Disputed->value => static::Disputed->label(),
        ];
    }

    public static function getStatusOptionsWithEmojis(): array
    {
        return [
            static::Unpaid->value => static::Unpaid->labelWithEmoji(),
            static::Paid->value => static::Paid->labelWithEmoji(),
            static::Late->value => static::Late->labelWithEmoji(),
            static::PartiallyPaid->value => static::PartiallyPaid->labelWithEmoji(),
            static::Cancelled->value => static::Cancelled->labelWithEmoji(),
            static::Refunded->value => static::Refunded->labelWithEmoji(),
            static::Disputed->value => static::Disputed->labelWithEmoji(),
        ];
    }
}
