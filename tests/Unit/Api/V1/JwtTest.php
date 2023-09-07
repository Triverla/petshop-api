<?php

namespace Tests\Unit\Api\V1;

use App\Helpers\Token;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JwtTest extends TestCase
{
    use RefreshDatabase;

    public function testTokenHelperCanEncode()
    {
        $payload = [
            'sub' => '1234567890',
            'name' => 'Test',
            'iat' => 1356999524,
            'exp' => Carbon::now()->addMinutes(config('petshop.jwt_max_lifetime'))->getTimestamp(),
        ];

        $token = Token::encodeJwt($payload);
        $decoded_token = Token::decodeJwt($token);

        $this->assertEquals($decoded_token->sub, '1234567890');
        $this->assertEquals($decoded_token->name, 'Test');
        $this->assertEquals($decoded_token->iat, 1356999524);
    }

    public function testTokenHelperCanDecode()
    {
        $payload = [
            'sub' => '1234567890',
            'name' => 'Test',
            'iat' => 1516239022,
            'exp' => Carbon::now()->addMinutes(config('petshop.jwt_max_lifetime'))->getTimestamp(),
        ];

        $token = Token::encodeJwt($payload);
        $decodedToken = Token::decodeJwt($token);
        $this->assertEquals($decodedToken, Token::decodeJwt($token));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Couldn't Decrypt your Token");
        Token::decodeJwt($token . 'invalid');
    }
}
