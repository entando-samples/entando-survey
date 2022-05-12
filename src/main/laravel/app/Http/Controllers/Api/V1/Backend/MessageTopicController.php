<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Models\MessageTopic;
use Illuminate\Http\Request;

class MessageTopicController extends Controller
{
    public function index()
    {
        return success(
            MessageTopic::query()
                ->latest()
                ->when(request('search'), fn ($q) => $q->where('title', 'like', '%' . request('search') . '%'))
                ->get()
        );
    }

    public function store(Request $request)
    {
        $attributes = $request->validate(['title' => ['required', 'string', 'max:255']]);

        $topic = MessageTopic::query()
            ->create($attributes);

        return success($topic, "Topic saved successfully..");
    }

    public function show(MessageTopic $messageTopic)
    {
        return success($messageTopic);
    }

    public function update(Request $request, MessageTopic $messageTopic)
    {
        $attributes = $request->validate(['title' => ['required', 'string', 'max:255']]);

        $messageTopic->update($attributes);

        return success(null, 'Topic updated successfully..');
    }

    public function destroy(MessageTopic $messageTopic)
    {
        $messageTopic->delete();

        return success(null, 'Topic deleted successfully');
    }

    public function messagesCount(MessageTopic $messageTopic)
    {
        return success([
            'count' => $messageTopic->messages()->count(),
        ]);
    }
}
