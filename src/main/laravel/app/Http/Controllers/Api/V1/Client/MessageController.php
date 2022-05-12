<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::query()
            ->withoutGlobalScope('not-archived')
            ->with('reply:message_id,read_at')
            ->forUser(auth()->user())
            ->latest()
            ->get();

        return MessageResource::collection($messages);
    }

    public function show($id)
    {
        $message = Message::query()
            ->withoutGlobalScope('not-archived')
            ->forUser(auth()->user())
            ->findOrFail($id);

        $reply = null;

        if (!$message->from_bo) {
            $reply = $message->reply()->first();
        };

        if (!$message->read_at) {
            $message
                ->update(['read_at' => now()]);
        }

        if (!optional($reply)->read_at) {
            $message->reply()->update(['read_at' => now()]);
        };

        return MessageResource::make($message)->additional([
            "reply" => optional($reply)->body ?? '',
        ]);
    }

    public function store(MessageRequest $request)
    {
        $attributes = $request->validated();

        $attributes = array_merge($attributes, [
            'sender_id' => auth()->id(),
            "topic_id" => $attributes['topic']
        ]);

        $message = Message::create($attributes);

        $message->load('topic');

        return MessageResource::make($message)
            ->additional([
                'message' => __("Message sent successfully")
            ]);
    }
}
