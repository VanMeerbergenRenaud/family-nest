<?php

namespace App\Enums;

enum PaymentFrequencyEnum: string
{
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
            static::Daily => 'Quotidien',
            static::Weekly => 'Hebdomadaire',
            static::Monthly => 'Mensuel',
            static::Quarterly => 'Trimestriel',
            static::SemiAnnually => 'Semestriel',
            static::Annually => 'Annuel',
            static::OneTime => 'Ponctuel',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            static::Daily => '📅',
            static::Weekly => '🗓️',
            static::Monthly => '📆',
            static::Quarterly => '🗂️',
            static::SemiAnnually => '📚',
            static::Annually => '🏛️',
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
            static::Monthly => 'blue',
            static::Quarterly => 'indigo',
            static::SemiAnnually => 'violet',
            static::Annually => 'purple',
            static::OneTime => 'gray',
        };
    }

    public function daysInterval(): ?int
    {
        return match ($this) {
            static::Daily => 1,
            static::Weekly => 7,
            static::Monthly => 30,
            static::Quarterly => 90,
            static::SemiAnnually => 182,
            static::Annually => 365,
            static::OneTime => null,
        };
    }

    public static function getFrequencyOptions(): array
    {
        return [
            static::Daily->value => static::Daily->label(),
            static::Weekly->value => static::Weekly->label(),
            static::Monthly->value => static::Monthly->label(),
            static::Quarterly->value => static::Quarterly->label(),
            static::SemiAnnually->value => static::SemiAnnually->label(),
            static::Annually->value => static::Annually->label(),
            static::OneTime->value => static::OneTime->label(),
        ];
    }

    public static function getFrequencyOptionsWithEmojis(): array
    {
        return [
            static::Daily->value => static::Daily->labelWithEmoji(),
            static::Weekly->value => static::Weekly->labelWithEmoji(),
            static::Monthly->value => static::Monthly->labelWithEmoji(),
            static::Quarterly->value => static::Quarterly->labelWithEmoji(),
            static::SemiAnnually->value => static::SemiAnnually->labelWithEmoji(),
            static::Annually->value => static::Annually->labelWithEmoji(),
            static::OneTime->value => static::OneTime->labelWithEmoji(),
        ];
    }
}
