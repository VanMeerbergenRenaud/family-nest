<?php

namespace App\Livewire\Pages\Dashboard;

use App\Enums\PaymentStatusEnum;

enum FilterStatus: string
{
    case All = 'all';
    case Paid = PaymentStatusEnum::Paid->value;
    case Cancelled = PaymentStatusEnum::Cancelled->value;
    case Refunded = PaymentStatusEnum::Refunded->value;

    public function label(): string
    {
        return match ($this) {
            self::All => 'Tous',
            self::Paid => PaymentStatusEnum::Paid->label(),
            self::Cancelled => PaymentStatusEnum::Cancelled->label(),
            self::Refunded => PaymentStatusEnum::Refunded->label(),
        };
    }
}
