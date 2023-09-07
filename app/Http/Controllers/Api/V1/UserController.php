<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CreateUserRequest;
use App\Http\Requests\Api\V1\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\JwtToken;
use App\Models\User;
use App\Services\Api\V1\Auth\AuthService;
use Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
use Throwable;

class UserController extends Controller
{
    public function __construct(protected readonly AuthService $authService)
    {
    }

    /**
     * @OA\Get(
     *     path="api/v1/user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
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
    public function index(): JsonResponse
    {
        $user = User::where('uuid', Auth::id())->firstOrFail();

        return response()->json(new UserResource($user));
    }

    /**
     * @OA\Post(
     *     path="api/v1/user/create",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *                @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *               @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="User address",
     *                 ),
     *               @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="User phone number",
     *                 ),
     *               @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preferences",
     *                     type="string",
     *                     enum={"0", "1"},
     *                 ),
     *                 required={"first_name", "last_name", "email","password","address","phone_number"}
     *             )
     *         )
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
     *
     * @throws Throwable
     */
    public function createUser(CreateUserRequest $request): JsonResponse
    {
        $validatedRequest = $request->validated();
        $user = User::create([
            'first_name' => $validatedRequest['first_name'],
            'last_name' => $validatedRequest['last_name'],
            'email' => $validatedRequest['email'],
            'password' => Hash::make($validatedRequest['password']),
            'is_admin' => false,
            'is_marketing' => $request->has('is_marketing') ? $validatedRequest['is_marketing'] : false,
        ]);

        event(new Registered($user));

        $token = $user->storeJWT();
        $this->authService->storeJWT($token['token']);

        return response()->json([
            'token' => $token['token'],
            'user' => new UserResource($user)
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="api/v1/user/edit",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *                @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *               @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="User address",
     *                 ),
     *               @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="User phone number",
     *                 ),
     *               @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preferences",
     *                     type="string",
     *                     enum={"0", "1"},
     *                 ),
     *                 required={"first_name", "last_name", "email","password","address","phone_number"}
     *             )
     *         )
     *    ),
     *    @OA\Response(
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
     * )
     */
    public function edit(UpdateUserRequest $request): JsonResponse
    {
        $user = User::where('uuid', Auth::id())->firstOrFail();
        $user->update($request->safe()->all());

        return response()->json(new UserResource($user));
    }

    /**
     * @OA\Delete(
     *     path="api/v1/user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
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
    public function destroy(): JsonResponse
    {
        $user = User::where('uuid', Auth::id())->firstOrFail();
        JwtToken::where('user_id', $user->uuid)->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
