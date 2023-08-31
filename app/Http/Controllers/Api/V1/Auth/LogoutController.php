<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\JwtToken;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        JwtToken::where('user_id', $request->user()->uuid)->delete();

        return response()->noContent();
    }
}
