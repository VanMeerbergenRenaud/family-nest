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

    public function labelWithEmoji(): string
    {
        return $this->emoji().'&nbsp;&nbsp;'.$this->label();
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

    public function notificationDays(): int
    {
        return match ($this) {
            self::Critical => 7,
            self::High => 5,
            self::Medium => 3,
            self::Low => 2,
            self::Minimal => 1,
            self::None => 0,
        };
    }

    public static function getPriorityOptions(): array
    {
        return [
            self::Critical->value => self::Critical->label(),
            self::High->value => self::High->label(),
            self::Medium->value => self::Medium->label(),
            self::Low->value => self::Low->label(),
            self::Minimal->value => self::Minimal->label(),
            self::None->value => self::None->label(),
        ];
    }

    public static function getPriorityOptionsWithEmojis(): array
    {
        return [
            self::Critical->value => self::Critical->labelWithEmoji(),
            self::High->value => self::High->labelWithEmoji(),
            self::Medium->value => self::Medium->labelWithEmoji(),
            self::Low->value => self::Low->labelWithEmoji(),
            self::Minimal->value => self::Minimal->labelWithEmoji(),
            self::None->value => self::None->labelWithEmoji(),
        ];
    }
}
