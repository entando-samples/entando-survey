<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Models\Credit;

class CreditController extends Controller
{
    public function show()
    {
        return success([
            'content' => Credit::get()
        ]);
    }

    public function update()
    {
        $attributes = request()->validate([
            'content' => ['required', 'nullable', 'string']
        ]);

        Credit::update($attributes['content']);

        return success();
    }
}
