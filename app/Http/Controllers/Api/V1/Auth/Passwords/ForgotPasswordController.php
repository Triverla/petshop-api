<?php

namespace App\Http\Controllers\Api\V1\Auth\Passwords;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{

    public function __invoke(ForgotPasswordRequest $request)
    {
        $validatedRequest = $request->validated();

        $user = User::where('email', $validatedRequest['email'])->firstOrFail();
        $token = Password::createToken($user);

        return response()->json(['token' => $token]);
    }
}
