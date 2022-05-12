<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageTopic extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'topic_id', 'id');
    }
}
