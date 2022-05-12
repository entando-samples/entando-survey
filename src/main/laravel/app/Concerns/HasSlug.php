<?php

namespace App\Concerns;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    public static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $slugColumn = static::slugColumn();

            if (!$model->{$slugColumn}) {
                $model->{$slugColumn} = Str::uuid();
            }
        });
    }

    public static function slugColumn(): string
    {
        return 'slug';
    }
}
