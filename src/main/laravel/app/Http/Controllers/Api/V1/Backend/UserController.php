<?php

namespace App\Http\Controllers\Api\V1\Backend;


use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function me()
    {

        return Auth::user();
//        return success(['user' => auth()->user()]);
    }
}
