<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\SendPasswordResetEmail;

class UserController extends Controller
{

    public function me()
    {

        return Auth::user();
//        return success(['user' => auth()->user()]);
    }
}
