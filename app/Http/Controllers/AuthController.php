<?php

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $req)
    {
        $validated = $req->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $req)
    {
        $credentials = $req->validated();

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged in',
            'token' => $token,
        ], 201);
    }

    public function logout(Request $req)
    {
        JWTAuth::invalidate($req->bearerToken());

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ], 200);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'token' => JWTAuth::refresh(),
        ]);
    }
}
