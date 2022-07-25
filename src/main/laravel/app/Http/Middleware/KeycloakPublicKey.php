<?php

namespace App\Http\Middleware;

use App\Services\KeycloakAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KeycloakPublicKey
{
    public function handle(Request $request, \Closure $next)
    {
        $keycloakAuthService = new KeycloakAuthService();
        try {
            if ($request->headers->has('Authorization')) {
                $publicKey = $keycloakAuthService->handle($request->header('Authorization'));

                config(['keycloak.realm_public_key'=>$publicKey]);
            }

            return $next($request);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            if (!$request->expectsJson()) {
                abort(401);
            }
            else {
                return response()->json(['message'=>'Keycloak error'],500);
            }
        }


    }
}