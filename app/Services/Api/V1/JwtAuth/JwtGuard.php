<?php

namespace App\Services\Api\V1\JwtAuth;

use App\Helpers\Token;
use App\Models\JwtToken;
use Carbon\Carbon;
use DomainException;
use Exception;
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
use UnexpectedValueException;

class JwtGuard implements Guard
{
    use GuardHelpers, Macroable;

    /**
     * The request instance.
     *
     * @var Request
     */
    protected Request $request;
    private $token;

    /**
     * Create a new authentication guard.
     *
     * @param UserProvider $provider
     * @param Request $request
     *
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['token'])) {
            return false;
        }

        if ($this->provider->retrieveByCredentials(['id' => $credentials['token']])) {
            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
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
            abort(Response::HTTP_BAD_REQUEST, 'Invalid token. Kindly login');
        }

        $this->enforceShortLivedTokens($token);

        $this->token = $token;
        $this->user = $this->provider->retrieveByCredentials(['uuid' => $token->user_id]);

        return $this->user;
    }

    public function validateJwtExpiryTime($token): ?bool
    {
        $storedToken = JwtToken::with('user')->currentUser($token->user_id)->first();
        if (!app()->environment('testing')) {
            if (empty($storedToken)) {
                return null;
            }

            if ($storedToken->isExpired()) {
                abort(Response::HTTP_UNAUTHORIZED, 'Token expired, please renew your jwt');
            }
        }

        return true;
    }

    private function getTokenFromRequest()
    {
        $request = request();
        return $request->token ?? $request->bearerToken();
    }

    private function handleMissingToken()
    {
        if (config('app.debug')) {
            throw new UnexpectedValueException(
                'Missing token. Please provide Bearer Authorization in request header.'
            );
        }

        return null;
    }

    private function validateToken($token): ?\stdClass
    {
        try {
            $decodedToken = Token::decodeJwt($token);
            $expired = $this->validateJWTExpiryTime($decodedToken);

            if (is_null($expired)) {
                return null;
            }

            return $decodedToken;
        } catch (ExpiredException $e) {
            return null;
        } catch (SignatureInvalidException $e) {
            if (app()->environment('testing')) {
                return throw $e;
            }
            return null;
        } catch (DomainException|InvalidArgumentException|UnexpectedValueException $e) {
            if (config('app.debug')) {
                throw $e;
            }
            \Log::error($e);
            return null;
        } catch (Exception $e) {
            if (config('app.debug')) {
                throw $e;
            }
            return null;
        }
    }

    private function enforceShortLivedTokens($token)
    {
        $maxMinutes = config('petshop.jwt_max_lifetime');
        if (empty($token->exp)) {
            throw new UnexpectedValueException('Token exceeds maximum lifetime.');
        }

        if ($token->exp > strtotime("+{$maxMinutes} minutes")) {
            $validMinutes = Carbon::createFromTimestamp($token->exp)->diffInMinutes();
            throw new UnexpectedValueException(
                'Token exceeds maximum lifetime. Token is valid for ' . $validMinutes . ' minutes, but max lifetime is ' . $maxMinutes . ' minutes.'
            );
        }
    }

}
