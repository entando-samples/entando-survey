<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDocument extends Model
{
    use HasFactory;

    protected $touches = [
        'folder'
    ];

    protected $fillable = [
        'title',
        'note',
        'attachments',
        'voice_message',
        'folder_id',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
    ];

    protected $attributes = [
        'attachments' => '[]'
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(PatientFolder::class, 'folder_id', 'id');
    }

    public function scopeUnreadDocuments($query)
    {
        return $query->whereNull('read_at');
    }

    public function patient(){
        return $this->belongsTo(User::class,'patient_id','id');
    }
}
