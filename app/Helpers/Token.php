<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class Token
{
    public static function encodeJwt($payload): string
    {
        if (!isset($payload['exp'])) {
            $hours = config('confirmation_tokens.exp_hours');
            $payload['exp'] = strtotime("+{$hours} hours");
        }

        return JWT::encode($payload, config('petshop.jwt_secret'), config('petshop.jwt_alg')[0]);
    }

    public static function decodeJwt($token): \stdClass
    {
        try {
            return JWT::decode($token, new Key(config('petshop.jwt_secret'), config('petshop.jwt_alg')[0]));
        } catch (SignatureInvalidException $e) {
            return throw new Exception("Couldn't Decrypt your Token");
        }
    }

    public static function encryptJwt($args): string
    {
        return encrypt(self::encodeJwt($args));
    }

    /**
     * @throws Exception
     */
    public static function decryptJwt($token): \stdClass
    {
        return self::decodeJwt(decrypt($token));
    }
}
