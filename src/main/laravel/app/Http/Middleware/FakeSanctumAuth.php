<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class FakeSanctumAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $bearer = ltrim(($request->header('Authorization') ?? ''), 'Bearer');

        try {
            [$id, $token] = explode("|", $bearer);
        } catch (\Exception $e) {
            return problem(__("Invalid bearer"), 401);
        }

        $id = (int) $id;

        if ($id === 1 || !User::where("id", $id)->exists()) {
            return problem(__("Not authenticated"), 401);
        }


        if ($token === 'secret-token') {
            auth()->login(User::find($id));
            return $next($request);
        }

        return problem(__("Not authenticated"), 401);
    }
}
