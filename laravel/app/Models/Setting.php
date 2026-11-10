<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $row = static::query()->where('key', $key)->first();

        return $row ? $row->value : $default;
    }

    public static function setValue(string $key, ?string $value): self
    {
        return static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
