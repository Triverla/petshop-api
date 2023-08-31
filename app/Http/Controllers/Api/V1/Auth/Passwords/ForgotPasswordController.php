<?php

namespace App\Http\Controllers\Api\V1\Auth\Passwords;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use OpenApi\Annotations as OA;

class ForgotPasswordController extends Controller
{

    /**
     * @OA\Post(
     *     path="user/forgot-password",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User email",
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
    public function __invoke(ForgotPasswordRequest $request)
    {
        $validatedRequest = $request->validated();

        $user = User::where('email', $validatedRequest['email'])->firstOrFail();
        $token = Password::createToken($user);

        return response()->json(['token' => $token]);
    }
}
