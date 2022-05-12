<?php

namespace App\Models;

use App\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pathology extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
    ];

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }

    public function surveys(): BelongsToMany
    {
        return $this->belongsToMany(Survey::class);
    }
}
