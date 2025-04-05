<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum PriorityEnum: string
{
    use EnumLabelTrait;

    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
    case Minimal = 'minimal';
    case None = 'none';

    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Critique',
            self::High => 'Élevée',
            self::Medium => 'Moyenne',
            self::Low => 'Basse',
            self::Minimal => 'Minimale',
            self::None => 'Aucune',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Critical => '🚨',
            self::High => '⚠️',
            self::Medium => '📊',
            self::Low => '🔽',
            self::Minimal => '🕸️',
            self::None => '🤷',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Critical => 'rose',
            self::High => 'red',
            self::Medium => 'yellow',
            self::Low => 'blue',
            self::Minimal => 'teal',
            self::None => 'gray',
        };
    }

    public static function getPriorityOptions(): array
    {
        return self::getOptions();
    }

    public static function getPriorityOptionsWithEmojis(): array
    {
        return self::getOptionsWithEmojis();
    }
}
