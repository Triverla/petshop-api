<?php

namespace App\Services\Api\V1\JwtAuth;

use App\Helpers\Token;
use App\Models\JwtToken;
use Carbon\Carbon;
use DomainException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use stdClass;
use UnexpectedValueException;

class JwtGuard implements Guard
{
    use GuardHelpers, Macroable;

    protected Request $request;
    protected stdClass $token;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
    }

    public function validate(array $credentials = []): bool
    {
        return !empty($credentials['token']) && $this->provider->retrieveByCredentials(['id' => $credentials['token']]);
    }

    public function user(): ?Authenticatable
    {
        if ($this->user) {
            return $this->user;
        }

        $requestToken = $this->getTokenFromRequest();

        abort_if(
            !$requestToken,
            Response::HTTP_UNAUTHORIZED,
            'Missing token. Please provide Bearer Authorization in request header.'
        );

        $token = $this->validateToken($requestToken);

        $this->validateJwtExpiryTime($token);

        $this->enforceMaxLifetime($token, config('petshop.jwt_max_lifetime'));

        $this->token = $token;
        $this->user = $this->provider->retrieveByCredentials(['uuid' => $token->user_id]);

        return $this->user;
    }

    private function getTokenFromRequest()
    {
        return $this->request->token ?? $this->request->bearerToken();
    }

    private function validateToken($token): ?stdClass
    {
        try {
            $token = Token::decodeJwt($token);
        } catch (ExpiredException|SignatureInvalidException
        |DomainException|InvalidArgumentException
        |UnexpectedValueException $e) {
            $this->abortWithUnauthorizedResponse('Invalid or Expired token. Kindly login');
        }

        return $token;
    }

    private function validateJwtExpiryTime($token): ?bool
    {
        $storedToken = JwtToken::with('user')->currentUser($token->user_id)->first();

        abort_if(
            (!app()->runningInConsole() && (empty($storedToken) || $storedToken->isExpired())),
            Response::HTTP_UNAUTHORIZED,
            'Token expired, please renew your jwt'
        );

        return true;
    }

    private function enforceMaxLifetime(stdClass $token, int $maxMinutes): void
    {
        abort_if(
            (empty($token->exp) || $token->exp > strtotime("+{$maxMinutes} minutes")),
            Response::HTTP_UNAUTHORIZED,
            'Token exceeds maximum lifetime. Token is valid for ' . Carbon::createFromTimestamp(
                $token->exp
            )->diffInMinutes()
        );
    }

    private function abortWithUnauthorizedResponse(string $message): void
    {
        abort(Response::HTTP_UNAUTHORIZED, $message);
    }
}
