<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Services\Api\V1\Auth\AuthService;
use Exception;

class LoginController extends Controller
{

    public function __construct(protected readonly AuthService $authService)
    {
    }

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
