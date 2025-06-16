<?php

namespace App\Livewire\Pages\Dashboard;

use App\Enums\PaymentStatusEnum;

enum FilterStatusEnum: string
{
    case All = 'all';
    case Paid = PaymentStatusEnum::Paid->value;
    case Unpaid = PaymentStatusEnum::Unpaid->value;
    case Late = PaymentStatusEnum::Late->value;
    case PartiallyPaid = PaymentStatusEnum::PartiallyPaid->value;
    case Cancelled = PaymentStatusEnum::Cancelled->value;
    case Refunded = PaymentStatusEnum::Refunded->value;
    case Disputed = PaymentStatusEnum::Disputed->value;

    public function label(): string
    {
        return match ($this) {
            self::All => 'Tous les statuts',
            self::Paid => PaymentStatusEnum::Paid->label(),
            self::Unpaid => PaymentStatusEnum::Unpaid->label(),
            self::Late => PaymentStatusEnum::Late->label(),
            self::PartiallyPaid => PaymentStatusEnum::PartiallyPaid->label(),
            self::Cancelled => PaymentStatusEnum::Cancelled->label(),
            self::Refunded => PaymentStatusEnum::Refunded->label(),
            self::Disputed => PaymentStatusEnum::Disputed->label(),
        };
    }
}
