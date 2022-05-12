<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Models\Faq;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
    {
        $faq = Faq::sorted()->get()->map(function ($item) {
            static $position = 0;

            return  [
                "id" => $item['id'],
                "question" => $item['question'],
                "answer" => $item['answer'],
                "position" => ++$position,
            ];
        });

        return success($faq);
    }
}
