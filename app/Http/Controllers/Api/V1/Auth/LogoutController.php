<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\JwtToken;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class LogoutController extends Controller
{

    /**
     * @OA\Get(
     *     path="user/logout",
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
    public function __invoke(Request $request)
    {
        JwtToken::where('user_id', $request->user()->uuid)->delete();

        return response()->noContent();
    }
}
