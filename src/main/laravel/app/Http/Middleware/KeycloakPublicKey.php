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
        try{
            $publicKey = $keycloakAuthService->handle();

            config(['keycloak.realm_public_key'=>$publicKey]);
            return $next($request);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            abort(503);
        }


    }
}