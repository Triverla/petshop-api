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
    private stdClass $token;

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
        if (!$requestToken) {
            $this->handleMissingToken();
        }

        $token = $this->validateToken($requestToken);

        if (is_null($token)) {
            $this->abortWithUnauthorizedResponse('Invalid token. Kindly login');
        }

        $this->enforceShortLivedTokens($token);

        $this->token = $token;
        $this->user = $this->provider->retrieveByCredentials(['uuid' => $token->user_id]);

        return $this->user;
    }

    private function getTokenFromRequest()
    {
        return $this->request->token ?? $this->request->bearerToken();
    }

    private function handleMissingToken()
    {
        if (config('app.debug')) {
            throw new UnexpectedValueException('Missing token. Please provide Bearer Authorization in request header.');
        }
    }

    private function validateToken($token): ?stdClass
    {
        try {
            $decodedToken = Token::decodeJwt($token);
            if (!$this->validateJwtExpiryTime($decodedToken)) {
                return null;
            }
            return $decodedToken;
        } catch (ExpiredException $e) {
            return null;
        } catch (SignatureInvalidException $e) {
            if (app()->environment('testing')) {
                throw $e;
            }
            return null;
        } catch (DomainException|InvalidArgumentException|UnexpectedValueException $e) {
            if (config('app.debug')) {
                throw $e;
            }
            \Log::error($e);
            return null;
        }
    }

    private function validateJwtExpiryTime($token): ?bool
    {
        if (!app()->environment('testing')) {
            $storedToken = JwtToken::with('user')->currentUser($token->user_id)->first();
            if (empty($storedToken) || $storedToken->isExpired()) {
                $this->abortWithUnauthorizedResponse('Token expired, please renew your jwt');
            }
        }
        return true;
    }

    private function enforceShortLivedTokens(stdClass $token): void
    {
        $maxMinutes = config('petshop.jwt_max_lifetime');
        if (empty($token->exp) || $token->exp > strtotime("+{$maxMinutes} minutes")) {
            $validMinutes = Carbon::createFromTimestamp($token->exp)->diffInMinutes();
            throw new UnexpectedValueException('Token exceeds maximum lifetime. Token is valid for ' . $validMinutes);
        }
    }

    private function abortWithUnauthorizedResponse(string $message): void
    {
        abort(Response::HTTP_UNAUTHORIZED, $message);
    }
}

