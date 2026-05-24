<?php

namespace App\Traits;

trait EnumLabelTrait
{
    public function label(): string
    {
        return $this->value;
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji().'&nbsp;&nbsp;'.$this->label();
    }

    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public static function getOptionsWithEmojis(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->labelWithEmoji();
        }

        return $options;
    }

    /**
     * Safely get enum case from value, returning null if value is empty or invalid.
     */
    public static function tryFromValue(mixed $value): ?static
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof static) {
            return $value;
        }

        return static::tryFrom($value);
    }

    /**
     * Safely get enum case from value, returning null if value is empty, but throwing if invalid.
     */
    public static function fromValue(mixed $value): ?static
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof static) {
            return $value;
        }

        return static::from($value);
    }
}
