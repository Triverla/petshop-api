<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Services\Api\V1\Auth\AuthService;
use Exception;
use OpenApi\Annotations as OA;

class LoginController extends Controller
{

    /**
     * @param AuthService $authService
     */
    public function __construct(protected readonly AuthService $authService)
    {
    }

    /**
     * @OA\Post(
     * path="api/v1/admin/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Admin"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="admin@buckhill.co.uk"),
     *       @OA\Property(property="password", type="string", format="password", example="admin")
     *    ),
     * ),
     * @OA\Response(response="200", description="Success"),
     * @OA\Response(
     *    response=422,
     *    description="Invalid credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Invalid email address or password.")
     *        )
     *     )
     * )
     */
    public function __invoke(LoginRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $data = $this->authService->login($validatedData);

            return response()->json($data);
        } catch (Exception $e) {
            throw new Exception('Unsuccessful Login Attempt');
        }
    }

}
