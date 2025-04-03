<?php

namespace App\Enums;

enum PaymentFrequencyEnum: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Biweekly = 'biweekly';
    case Monthly = 'monthly';
    case Bimonthly = 'bimonthly';
    case Quarterly = 'quarterly';
    case SemiAnnually = 'semi_annually';
    case Annually = 'annually';
    case Biennially = 'biennially';
    case OneTime = 'one_time';

    public function label(): string
    {
        return match ($this) {
            static::Daily => 'Quotidien',
            static::Weekly => 'Hebdomadaire',
            static::Biweekly => 'Bi-hebdomadaire',
            static::Monthly => 'Mensuel',
            static::Bimonthly => 'Bi-mensuel',
            static::Quarterly => 'Trimestriel',
            static::SemiAnnually => 'Semestriel',
            static::Annually => 'Annuel',
            static::Biennially => 'Bi-annuel',
            static::OneTime => 'Ponctuel',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            static::Daily => '📅',
            static::Weekly => '🗓️',
            static::Biweekly => '🔄',
            static::Monthly => '📆',
            static::Bimonthly => '📋',
            static::Quarterly => '🗂️',
            static::SemiAnnually => '📚',
            static::Annually => '🏛️',
            static::Biennially => '⏳',
            static::OneTime => '⚡',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji() . '&nbsp;&nbsp;' . $this->label();
    }

    public function color(): string
    {
        return match ($this) {
            static::Daily => 'red',
            static::Weekly => 'orange',
            static::Biweekly => 'amber',
            static::Monthly => 'blue',
            static::Bimonthly => 'sky',
            static::Quarterly => 'indigo',
            static::SemiAnnually => 'violet',
            static::Annually => 'purple',
            static::Biennially => 'fuchsia',
            static::OneTime => 'gray',
        };
    }

    public function daysInterval(): ?int
    {
        return match ($this) {
            static::Daily => 1,
            static::Weekly => 7,
            static::Biweekly => 14,
            static::Monthly => 30,
            static::Bimonthly => 60,
            static::Quarterly => 90,
            static::SemiAnnually => 182,
            static::Annually => 365,
            static::Biennially => 730,
            static::OneTime => null,
        };
    }

    public static function getFrequencyOptions(): array
    {
        return [
            static::Daily->value => static::Daily->label(),
            static::Weekly->value => static::Weekly->label(),
            static::Biweekly->value => static::Biweekly->label(),
            static::Monthly->value => static::Monthly->label(),
            static::Bimonthly->value => static::Bimonthly->label(),
            static::Quarterly->value => static::Quarterly->label(),
            static::SemiAnnually->value => static::SemiAnnually->label(),
            static::Annually->value => static::Annually->label(),
            static::Biennially->value => static::Biennially->label(),
            static::OneTime->value => static::OneTime->label(),
        ];
    }

    public static function getFrequencyOptionsWithEmojis(): array
    {
        return [
            static::Daily->value => static::Daily->labelWithEmoji(),
            static::Weekly->value => static::Weekly->labelWithEmoji(),
            static::Biweekly->value => static::Biweekly->labelWithEmoji(),
            static::Monthly->value => static::Monthly->labelWithEmoji(),
            static::Bimonthly->value => static::Bimonthly->labelWithEmoji(),
            static::Quarterly->value => static::Quarterly->labelWithEmoji(),
            static::SemiAnnually->value => static::SemiAnnually->labelWithEmoji(),
            static::Annually->value => static::Annually->labelWithEmoji(),
            static::Biennially->value => static::Biennially->labelWithEmoji(),
            static::OneTime->value => static::OneTime->labelWithEmoji(),
        ];
    }
}
