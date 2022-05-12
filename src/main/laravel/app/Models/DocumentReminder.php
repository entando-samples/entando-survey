<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentReminder extends Model
{
    use HasFactory, HasEagerLimit;

    protected $fillable = [
        'pivot_id',
        'reminded_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reminded_by');
    }
}
