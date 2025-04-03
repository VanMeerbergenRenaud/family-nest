<?php

namespace App\Enums;

enum PriorityEnum: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
    case Minimal = 'minimal';
    case None = 'none';

    public function label(): string
    {
        return match ($this) {
            static::Critical => 'Critique',
            static::High => 'Élevée',
            static::Medium => 'Moyenne',
            static::Low => 'Basse',
            static::Minimal => 'Minimale',
            static::None => 'Aucune',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            static::Critical => '🚨',
            static::High => '⚠️',
            static::Medium => '📊',
            static::Low => '🔽',
            static::Minimal => '🕸️',
            static::None => '🤷',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji() . '&nbsp;&nbsp;' . $this->label();
    }

    public function color(): string
    {
        return match ($this) {
            static::Critical => 'rose',
            static::High => 'red',
            static::Medium => 'yellow',
            static::Low => 'blue',
            static::Minimal => 'teal',
            static::None => 'gray',
        };
    }

    public function notificationDays(): int
    {
        return match ($this) {
            static::Critical => 7,
            static::High => 5,
            static::Medium => 3,
            static::Low => 2,
            static::Minimal => 1,
            static::None => 0,
        };
    }

    public static function getPriorityOptions(): array
    {
        return [
            static::Critical->value => static::Critical->label(),
            static::High->value => static::High->label(),
            static::Medium->value => static::Medium->label(),
            static::Low->value => static::Low->label(),
            static::Minimal->value => static::Minimal->label(),
            static::None->value => static::None->label(),
        ];
    }

    public static function getPriorityOptionsWithEmojis(): array
    {
        return [
            static::Critical->value => static::Critical->labelWithEmoji(),
            static::High->value => static::High->labelWithEmoji(),
            static::Medium->value => static::Medium->labelWithEmoji(),
            static::Low->value => static::Low->labelWithEmoji(),
            static::Minimal->value => static::Minimal->labelWithEmoji(),
            static::None->value => static::None->labelWithEmoji(),
        ];
    }
}
