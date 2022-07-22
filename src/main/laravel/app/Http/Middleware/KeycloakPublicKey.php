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
//            dd($publicKey);
            config(['keycloak.realm_public_key'=>$publicKey]);
            return $next($request);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            if(!$request->expectsJson()):
                abort(500);
            else:
                return response()->json(['message'=>'Keycloak error'],500);
            endif;
        }


    }
}