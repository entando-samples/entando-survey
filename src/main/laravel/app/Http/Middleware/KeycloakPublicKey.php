<?php

namespace App\Http\Middleware;

use App\Services\KeycloakAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KeycloakPublicKey
{
    public function handle(Request $request, \Closure $next)
    {
        $auth = explode(' ', $request->header('Authorization'));
        $decoded_jwt = array_map('base64_decode', explode('.', $auth[1]));
        $params = json_decode($decoded_jwt[0], true);

        $keycloakAuthService = new KeycloakAuthService();
        try{
            $publicKey = $keycloakAuthService->handle($params);

            config(['keycloak.realm_public_key'=>$publicKey]);
            return $next($request);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            abort(503);
        }


    }
}