<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'position'
    ];

    protected $casts = [
        'position' => 'integer'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->position = (new static)->max('position') ?? 1;
        });
    }

    public function scopeSorted(Builder $q)
    {
        return $q->orderByRaw("ISNULL(position) ASC, position ASC")
            ->orderBy("created_at");
    }
}
