<?php

namespace App\Casts;

use App\Enums\PriorityEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PriorityEnumCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \App\Enums\PriorityEnum|null
     */
    public function get($model, string $key, $value, array $attributes): ?PriorityEnum
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $value instanceof PriorityEnum ? $value : PriorityEnum::tryFrom($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string|null
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $value instanceof PriorityEnum ? $value->value : $value;
    }
}
