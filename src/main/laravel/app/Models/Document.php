<?php

namespace App\Models;

use App\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory, HasSlug, HasEagerLimit;

    protected $fillable = [
        "title",
        "slug",
        "body",
        "youtube_url",
        'cover_image',
        'file',
        'images',
        "created_by",
        "updated_by",
    ];

    protected $attributes = [
        'images' => '[]'
    ];

    protected $casts = [
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSearch($q, $value)
    {
        if (!$value) return $q;

        $value = "%{$value}%";

        return $q
            ->where('title', 'like', $value)
            ->orWhere('body', 'like', $value);
    }

    public function scopeWhereInPathologies($q, $ids)
    {
        if (!$ids) return $q;

        if (!is_array($ids)) $ids = [$ids];

        if (empty($ids)) return $q;

        return $q->whereHas('pathologies', fn ($sq) => $sq->whereIn('pathologies.id', $ids));
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function lastUpdator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function pathologies()
    {
        return $this->belongsToMany(Pathology::class, 'document_pathology');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }

    public function patients()
    {
        return $this->belongsToMany(User::class, 'document_patient', 'document_id', 'patient_id');
    }

    public function reminders()
    {
        return $this->hasManyThrough(DocumentReminder::class, DocumentPatientPivot::class, 'document_id', 'pivot_id')
            ->latest();
    }
}
