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
}
