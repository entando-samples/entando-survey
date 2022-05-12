<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            $request->session()->invalidate();

        } catch (Exception $err) {
            return problem(__("Failed to logout. Try again!"));
        }

        return success($request->user()->currentAccessToken(), __("Logged out successfully from backedn"))
            ->withCookie(cookie()->forget('__access_token__'));
    }
}
