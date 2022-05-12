<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ForgetPasswordController extends Controller
{

    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [trans('Provided email is not registered! Please contact administration.')]
            ]);
        }
        $subject = "rkare - reset password";
        $body = 'You are receiving this email because you have requested for password reset, 
        please change your password to something strong';

        SendPasswordResetEmail::dispatch($user, $subject, $body);
        return success([], "Please check your email for a message with reset link.");
    }
}
