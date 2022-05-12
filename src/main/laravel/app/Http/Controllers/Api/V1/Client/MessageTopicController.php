<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\MessageTopic;

class MessageTopicController extends Controller
{
    public function index()
    {
        return ['data' => MessageTopic::get(['id', 'title'])];
    }
}
