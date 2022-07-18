<?php

namespace App\Services;

use CoderCat\JWKToPEM\JWKConverter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class KeycloakAuthService
{
    /**
     * @throws \CoderCat\JWKToPEM\Exception\Base64DecodeException
     * @throws \CoderCat\JWKToPEM\Exception\JWKConverterException
     * @throws \Exception
     */
    public function handle()
    {
        return cache()->remember('realm_public_key',60*2,function(){
            $certs = $this->getCert();

            $publicKey = $this->getPublicKey($certs);

            return $this->stringifyPublicKey($publicKey);
        });

    }

    private function getCert()
    {
        return Http::get(env('KEYCLOAK_AUTH_URL').'/realms/'.env('KEYCLOAK_REALM').'/protocol/openid-connect/certs')
            ->json();

    }

    /**
     * @throws \CoderCat\JWKToPEM\Exception\Base64DecodeException
     * @throws \CoderCat\JWKToPEM\Exception\JWKConverterException
     */
    protected function getPublicKey(array $certs)
    {
//        dd($certs['keys'][0]);
        $jwk = Arr::only($certs['keys'][0],['kty','kid','use','alg','e','n']);

        $jwkConverter = new JWKConverter();
        return $jwkConverter->toPEM($jwk);
    }

    protected function stringifyPublicKey(string $publicKey): string
    {
        return  preg_replace('/^.+\r|\n/', '', $publicKey);
    }
}