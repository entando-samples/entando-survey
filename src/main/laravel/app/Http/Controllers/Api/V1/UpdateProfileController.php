<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdateProfileController extends Controller
{
    public function __invoke(ProfileUpdateRequest $request)
    {
        $data = $request->validated();
        $user = User::findOrFail(auth()->user()->id);
        if (!$user || !Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Your password is incorrect!'],
            ]);
        }

        User::where('id', auth()->user()->id)
            ->update(['password' => Hash::make($data['password'])]);
            
        return success([], __("Password updated successfully."))
            ->withCookie(cookie()->forget('__access_token__'));
    }
}
