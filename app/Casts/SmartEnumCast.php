<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use BackedEnum;

class SmartEnumCast implements CastsAttributes
{
    /**
     * The enum class to cast to.
     */
    protected string $enumClass;

    /**
     * Create a new cast instance.
     */
    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof $this->enumClass) {
            return $value;
        }

        return $this->enumClass::tryFrom($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof $this->enumClass) {
            return $value->value;
        }

        return $value;
    }
}
