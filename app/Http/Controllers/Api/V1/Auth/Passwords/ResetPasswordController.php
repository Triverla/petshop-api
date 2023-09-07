<?php

namespace App\Http\Controllers\Api\V1\Auth\Passwords;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\PasswordUpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use OpenApi\Annotations as OA;

class ResetPasswordController extends Controller
{

    /**
     * @OA\Post(
     *     path="api/v1/user/reset-password-token",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="User reset token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function __invoke(PasswordUpdateRequest $request)
    {
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been successfully updated']);
        }

        if ($response === Password::INVALID_TOKEN) {
            abort(Response::HTTP_BAD_REQUEST, 'Invalid or expired token');
        }

        abort(Response::HTTP_BAD_REQUEST, 'Invalid or expired token');
    }
}
