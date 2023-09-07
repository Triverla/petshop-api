<?php

namespace App\Services\Api\V1\Auth;

use App\Helpers\Token;
use App\Models\JwtToken;
use Carbon\Carbon;
use Exception;
use http\Exception\UnexpectedValueException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthService
{

    public function login(array $data, bool $isAdmin = true): array
    {
        if (!Auth::attempt($data)) {
            throw new Exception('Invalid login credentials');
        }

        $user = Auth::guard()->user();

        if($isAdmin && !$user->is_admin){
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        if(!$isAdmin && $user->is_admin){
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        $device = substr(request()->userAgent() ?? '', 0, 255);

        $token = Token::encodeJwt([
            'user_id' => $user->uuid,
            'device' => $device,
            'iss' => config('app.url'),
            'exp' => Carbon::now()->addMinutes(config('petshop.jwt_max_lifetime'))->getTimestamp(),
        ]);

        $tokenExpiry = Carbon::createFromTimestamp(Token::decodeJwt($token)->exp);
        $storeJWT = $this->storeJWT($token);

        return [
            'unique_id' => $storeJWT->unique_id,
            'token' => $token,
            'token_expiry_text' => $tokenExpiry->diffForHumans(),
            'token_expiry_seconds' => $tokenExpiry->diffInSeconds(),
        ];
    }

    /**
     * @param string $token
     * @return mixed
     * @throws Exception
     */
    public function storeJWT(string $token): mixed
    {
        $tokenExpiry = Carbon::createFromTimestamp(Token::decodeJwt($token)->exp);

        $userId = Auth::id();

        JwtToken::where('user_id', $userId)->delete();

        return JwtToken::create([
            'user_id' => $userId,
            'token_title' => 'auth',
            'restrictions' => '{"restrictions": "*"}',
            'permissions' => '{"permissions": "*"}',
            'expires_at' => $tokenExpiry,
            'last_used_at' => Carbon::now(),
            'refreshed_at' => Carbon::now(),
        ]);
    }
}
