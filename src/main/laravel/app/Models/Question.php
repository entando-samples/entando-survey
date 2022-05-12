<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'protocol',
        'p_question',
        'key',
        'order',
    ];

    protected $casts = [
        'order' => 'integer'
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function surveys(): BelongsToMany
    {
        return $this->belongsToMany(Survey::class);
    }

    public function scopeWhereInQuestions($q, $questions)
    {
        if (!$questions) return $q;

        if (!is_array($questions)) $questions = [$questions];

        if (empty($questions)) return $q;

        return $q->whereIn('p_question', $questions);
    }

    public function scopeWhereInProtocols($q, $protcols)
    {
        if (!$protcols) return $q;

        if (!is_array($protcols)) $protcols = [$protcols];

        if (empty($protcols)) return $q;

        return $q->whereIn('protocol', $protcols);
    }

    public function scopeSearch($q, $value)
    {
        if (!$value) return $q;

        $value = "%{$value}%";

        return $q
            ->where('title', 'like', $value)
            ->orWhere('description', 'like', $value)
            ->orWhere('key', 'like', $value);
    }
}
