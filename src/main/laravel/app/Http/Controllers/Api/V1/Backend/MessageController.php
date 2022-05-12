<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MessageTopic;
use App\Rules\IsPatient;

class MessageController extends Controller
{
    public function index($listType)
    {
        if (!in_array($listType, ["inbound", "read", "require_call", "archived", "outbound"])) abort(404);

        if ($listType === 'require_call') $listType = "requireCall";

        $messages = Message::{$listType}()
            ->ofTopics(request("topics"))
            ->search(request('search'))
            ->latest()
            ->with('sender:id,name')
            ->when($listType === "outbound", fn ($q) => $q->with('receiver:id,name'))
            ->paginate();

        return success($messages);
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'topic' => ['required', 'numeric', 'exists:message_topics,id'],
            'patient' => ['required', 'numeric', IsPatient::new()],
            'body' => ['required', 'string', 'max:5000']
        ]);

        $message = Message::query()->create([
            'topic_id' => $attributes['topic'],
            'receiver_id' => $attributes['patient'],
            'sender_id' => auth()->id(),
            'body' => $attributes['body'],
            'from_bo' => true
        ]);

        return success($message);
    }

    public function show($id)
    {
        $message =  Message::withoutGlobalScope('not-archived')->findOrFail($id);

        if (request('markAsSeen') == "true" && !$message->from_bo) {
            $message->update(["read_at" => now(), "read_by" => auth()->id()]);
        }

        $message->load(['sender:id,name', 'reply' => fn ($q) => $q->with('author:id,name')]);

        return success($message);
    }

    public function configs()
    {
        return [
            'counts' => [
                'inbound' => Message::inbound()->count(),
                'read' => Message::read()->count(),
                'require_call' => Message::requireCall()->count(),
                'archived' => Message::archived()->count(),
                'outbound' => Message::outbound()->count(),
            ],
            'topics' => MessageTopic::get(['title as label', 'id'])
        ];
    }

    public function reply(Request $request, Message $message)
    {
        if ($message->reply()->exists()) abort(400, "Reply already exists");
        if ($message->from_bo) abort(400, "Cannot reply to message");

        $attributes = $request->validate([
            'body' => ['required', 'string', 'max:3000']
        ]);

        $reply = $message->reply()->create([
            'body' => $attributes['body'],
            'author_id' => auth()->id()
        ]);

        return success($reply);
    }

    public function markAsRequireCall(Message $message)
    {
        if ($message->from_bo) abort(400, "Invalid action");

        if (!$message->require_call) $message->update(['require_call' => true]);

        return success();
    }

    public function markAsCalled(Message $message)
    {
        if ($message->from_bo) abort(400, "Invalid action");

        if ($message->require_call && $message->called_at) return success();

        $message->update([
            "require_call" => true,
            "called_at" => now(),
        ]);

        return success();
    }

    public function inboundCount()
    {
        return success(
            Message::inbound()->count()
        );
    }
}
