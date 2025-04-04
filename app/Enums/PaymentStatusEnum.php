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
            self::Unpaid => 'Non payée',
            self::Paid => 'Payée',
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
            self::Unpaid => '⏳',
            self::Paid => '✅',
            self::Late => '⚠️',
            self::PartiallyPaid => '💰',
            self::Cancelled => '❌',
            self::Refunded => '↩️',
            self::Disputed => '⚖️',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji().'&nbsp;&nbsp;'.$this->label();
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid => 'gray',
            self::Paid => 'green',
            self::Late => 'red',
            self::PartiallyPaid => 'yellow',
            self::Cancelled => 'slate',
            self::Refunded => 'purple',
            self::Disputed => 'orange',
        };
    }

    public static function getStatusOptions(): array
    {
        return [
            self::Unpaid->value => self::Unpaid->label(),
            self::Paid->value => self::Paid->label(),
            self::Late->value => self::Late->label(),
            self::PartiallyPaid->value => self::PartiallyPaid->label(),
            self::Cancelled->value => self::Cancelled->label(),
            self::Refunded->value => self::Refunded->label(),
            self::Disputed->value => self::Disputed->label(),
        ];
    }

    public static function getStatusOptionsWithEmojis(): array
    {
        return [
            self::Unpaid->value => self::Unpaid->labelWithEmoji(),
            self::Paid->value => self::Paid->labelWithEmoji(),
            self::Late->value => self::Late->labelWithEmoji(),
            self::PartiallyPaid->value => self::PartiallyPaid->labelWithEmoji(),
            self::Cancelled->value => self::Cancelled->labelWithEmoji(),
            self::Refunded->value => self::Refunded->labelWithEmoji(),
            self::Disputed->value => self::Disputed->labelWithEmoji(),
        ];
    }
}
