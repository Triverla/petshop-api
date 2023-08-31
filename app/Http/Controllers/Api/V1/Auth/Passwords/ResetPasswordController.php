<?php

namespace App\Http\Controllers\Api\V1\Auth\Passwords;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\PasswordUpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{

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
