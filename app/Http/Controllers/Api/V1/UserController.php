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

class UserController extends Controller
{

    public function __construct(protected readonly AuthService $authService)
    {
    }

    public function index(): JsonResponse
    {
        $user = User::where('uuid', Auth::id())->firstOrFail();

        return response()->json(new UserResource($user));
    }

    public function createUser(CreateUserRequest $request)
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

    public function edit(UpdateUserRequest $request): JsonResponse
    {
        $user = User::where('uuid', Auth::id())->firstOrFail();
        $user->update($request->safe()->all());

        return response()->json(new UserResource($user));
    }

    public function destroy(): JsonResponse
    {
        $user = User::where('uuid', Auth::id())->firstOrFail();
        JwtToken::where('user_id', $user->uuid)->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
