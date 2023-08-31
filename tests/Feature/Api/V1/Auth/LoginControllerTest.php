<?php

namespace Tests\Feature\Api\V1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testMissingValuesInLoginRequestReturnsValidationError(): void
    {
        $this->postJson('api/v1/admin/login', [])
            ->assertInvalid(['email', 'password']);
    }
}
