<?php

namespace Tests\Feature\Api\V1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Base;

class ResetPasswordTest extends Base
{
    use RefreshDatabase;

    public function testUserCanGeneratePasswordResetToken()
    {
        $response = $this->postJson('/api/v1/user/forgot-password', [
            'email' => $this->user->email
        ]);

        $response->assertStatus(200);
    }

    public function testUserCanChangePassword()
    {
        $email = $this->user->email;
        $tokenResponse = $this->postJson('/api/v1/user/forgot-password', [
            'email' => $email
        ]);


        $response = $this->postJson('/api/v1/user/reset-password-token', [
            'email' => $email,
            "token" => $tokenResponse->json()['token'],
            "password" => "mypassword",
            "password_confirmation" => "mypassword"
        ]);

        $response->assertStatus(200);
    }
}
