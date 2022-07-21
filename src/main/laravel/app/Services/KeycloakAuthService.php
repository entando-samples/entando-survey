<?php

namespace App\Services;

use CoderCat\JWKToPEM\JWKConverter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeycloakAuthService
{
    /**
     * @throws \CoderCat\JWKToPEM\Exception\Base64DecodeException
     * @throws \CoderCat\JWKToPEM\Exception\JWKConverterException
     * @throws \Exception
     */
    public function handle(string $authorizationString)
    {
        return cache()->remember('realm_public_key',60*2, function() use ($authorizationString) {
            $auth = explode(' ', $authorizationString);
            $decoded = array_map('base64_decode', explode('.', $auth[1]));
            $params = json_decode($decoded[0], true);

            $certs = $this->getCert();

            $publicKey = $this->getPublicKey($certs, $params);

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
    protected function getPublicKey(array $certs, array $params)
    {
        foreach ($certs['keys'] as $item) {
            if ($item['kid'] === $params['kid']) {
                $jwk = Arr::only($item,['kty','kid','use','alg','e','n']);

                $jwkConverter = new JWKConverter();
                return $jwkConverter->toPEM($jwk);
            }
        }
    }

    protected function stringifyPublicKey(string $publicKey): string
    {
        return  preg_replace('/^.+\r|\n/', '', $publicKey);
    }
}