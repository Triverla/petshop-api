<?php

namespace Tests;

use App\Helpers\Token;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class Base extends TestCase
{
    public User $user;
    public User $admin;
    public User $marketingUser;
    public User $marketingAdminUser;
    public User $buckHillAdmin;
    public string $jwtAdminToken;
    public string $jwtUserToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->user = $this->createUser(false, false, ['password' => Hash::make('userpassword')]);
        $this->admin = $this->createUser(isAdmin: true);

        $this->buckHillAdmin = User::factory()->create([
            'first_name' => 'BuckHillAdmin',
            'email' => strtolower(fake()->firstName).'@buckhill.co.uk',
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);

        $payload = [
            'user_id' => $this->buckHillAdmin->uuid,
            'exp' => Carbon::now()->addMinutes(config('petshop.jwt_max_lifetime'))->getTimestamp(),
        ];
        $this->jwtAdminToken = $this->createJWTToken($payload);

        $payload = [
            'user_id' => $this->user->uuid,
            'exp' => Carbon::now()->addMinutes(config('petshop.jwt_max_lifetime'))->getTimestamp(),
        ];
        $this->jwtUserToken = $this->createJWTToken($payload);
    }

    private function createUser(bool $isAdmin = false, bool $isMarketing = false, ...$options): User
    {
        $data = [
            'is_admin' => $isAdmin,
            'is_marketing' => $isMarketing
        ];

        if (!empty($options)) {
            foreach ($options as $key => $optValue) {
                foreach ($optValue as $actKey => $realVal) {
                    $data[$actKey] = $realVal;
                }
            }
        }
        return User::factory()->create($data);
    }

    private function createJWTToken(array $user): string
    {
        return Token::encodeJwt($user);
    }
}
