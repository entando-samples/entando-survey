<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    protected $with = [
        'topic:id,title'
    ];

    protected $fillable = [
        'topic_id',
        'body',
        'attachments',
        'voice_message',
        'sender_id',
        'receiver_id',
        'read_by',
        'read_at',
        'require_call',
        'called_at',
        'is_archived',
        'from_bo',
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
        'called_at' => 'datetime',
        'require_call' => 'boolean',
        'is_archived' => 'boolean',
        'from_bo' => 'boolean',
    ];

    protected $attributes = [
        'attachments' => '[]'
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('not-archived', function (Builder $q) {
            $q->where("is_archived", false);
        });

        static::updating(function (Message $model) {
            if ($model->canBeArchived()) {
                $model->is_archived = true;
            }
        });
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(MessageTopic::class, 'topic_id', 'id');
    }

    public function reply(): HasOne
    {
        return $this->hasOne(MessageReply::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function scopeOfTopics(Builder $q, $topics)
    {
        if (!$topics || $topics == []) return;

        return $q->whereIn("topic_id", $topics);
    }

    public function scopeUnread(Builder $q)
    {
        return $q->whereNull("read_at");
    }

    public function scopeInbound(Builder $q)
    {
        return $q
            ->where('from_bo', false)
            ->whereNull("read_at")
            ->whereNull("called_at");
    }

    public function scopeRead(Builder $q)
    {
        return $q
            ->where('from_bo', false)
            ->whereNotNull("read_at");
    }

    public function scopeRequireCall(Builder $q)
    {
        return $q
            ->where('from_bo', false)
            ->where("require_call", true)
            ->whereNull('called_at');
    }

    public function scopeArchived(Builder $q)
    {
        return $q
            ->withoutGlobalScope('not-archived')
            ->where('from_bo', false)
            ->where("is_archived", true);
    }

    public function scopeOutbound($q)
    {
        return $q->where('from_bo', true);
    }

    public function scopeSearch($q, $value)
    {
        if (!$value) return $q;

        $value = "%{$value}%";

        return $q
            ->where('body', 'like', $value);
    }

    public function scopeForUser($q, User $user)
    {
        return $q
            ->where(function ($q) use ($user) {
                $q->where("sender_id", $user->id)
                    ->orWhere("receiver_id", $user->id);
            });
    }

    public function canBeArchived()
    {
        if ($this->from_bo) return false;

        $hasReply = $this->reply()->exists();

        return (!$this->require_call && $hasReply)  || ($this->require_call && $this->called_at && $hasReply);
    }

    public function archive()
    {
        return $this->update([
            'is_archived' => true
        ]);
    }
}
