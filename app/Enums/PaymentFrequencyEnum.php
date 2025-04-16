<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum PaymentFrequencyEnum: string
{
    use EnumLabelTrait;

    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case SemiAnnually = 'semi_annually';
    case Annually = 'annually';
    case OneTime = 'one_time';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Quotidien',
            self::Weekly => 'Hebdomadaire',
            self::Monthly => 'Mensuel',
            self::Quarterly => 'Trimestriel',
            self::SemiAnnually => 'Semestriel',
            self::Annually => 'Annuel',
            self::OneTime => 'Unique',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Daily => '📅',
            self::Weekly => '🗓️',
            self::Monthly => '📆',
            self::Quarterly => '🗂️',
            self::SemiAnnually => '📖',
            self::Annually => '🎈',
            self::OneTime => '⚡',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Daily => 'red',
            self::Weekly => 'orange',
            self::Monthly => 'blue',
            self::Quarterly => 'indigo',
            self::SemiAnnually => 'violet',
            self::Annually => 'purple',
            self::OneTime => 'gray',
        };
    }

    public function daysInterval(): ?int
    {
        return match ($this) {
            self::Daily => 1,
            self::Weekly => 7,
            self::Monthly => 30,
            self::Quarterly => 90,
            self::SemiAnnually => 182,
            self::Annually => 365,
            self::OneTime => null,
        };
    }

    public static function getFrequencyOptions(): array
    {
        return self::getOptions();
    }

    public static function getFrequencyOptionsWithEmojis(): array
    {
        return self::getOptionsWithEmojis();
    }
}
