<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        $result = $this->userService->authenticate($request);

        return response()->json($result['data'], $result['status']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'berhasil',
        ], 200);
    }
}
