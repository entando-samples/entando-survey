<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return problem('Incorrect email or password', 403);
        }

        $token = Auth::user()->createToken('api-token');

        return success([
            'user' => Auth::user(),
            'token' => $token->plainTextToken,
            "notification_id" => auth()->user()->getNotifiableId(),
        ], __("Logged in successfully"))
            ->withCookie(cookie('__access_token__', $token->plainTextToken, (60 * 24) * 30));
    }
}
