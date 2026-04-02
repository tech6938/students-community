<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{

    public function auth(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            // Check user
            $user = User::where('email', $request->email)->first();

            $isNewUser = false;

            if (!$user) {
                // REGISTER
                $user = User::create([
                    'email' => $request->email,
                    'profile_status' => 0,
                ]);

                $isNewUser = true;
            }

            // LOGIN (token)
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => $isNewUser
                    ? 'User registered successfully'
                    : 'User login successfully',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully'
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
