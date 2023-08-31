<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Contracts\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CreateUserRequest;
use App\Http\Requests\Api\V1\Admin\UpdateUserRequest;
use App\Http\Requests\Api\V1\Auth\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Api\V1\Auth\AuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct(protected readonly AuthService $authService)
    {
    }


    public function index(Request $request, Paginator $paginator)
    {
        $query = User::query()->where('is_admin', false);

        return $paginator->paginateData($request, $query);
    }

    public function update(User $user, UpdateUserRequest $request): JsonResponse
    {
        $attributes = $request->safe()->all();

        $user->update($attributes);

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
            'is_admin' => $request->has('is_admin') ? $validatedRequest['is_admin'] : false,
            'is_marketing' => $request->has('is_marketing') ? $validatedRequest['is_marketing'] : false,
        ]);

        event(new Registered($user));

        $token = $user->storeJWT();
        $storedJWT = $this->authService->storeJWT($token['token']);

        return response()->json([
            'token_unique_id' => $storedJWT->unique_id,
            'access_token' => $token['token'],
            'token_expiry_text' => $token['token_expiry_text'],
            'token_expiry_seconds' => $token['token_expiry_seconds'],
        ], Response::HTTP_CREATED);
    }

    public function destroy(User $user): Response
    {
        abort_if($user->is_admin, Response::HTTP_FORBIDDEN, 'User cannot be deleted');

        $user->delete();

        return response()->noContent();
    }
}
