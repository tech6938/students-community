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

            if (!$user) {
                // REGISTER
                $user = User::create([
                    'email' => $request->email,
                    'profile_status' => 0,
                ]);
            }

            // LOGIN (token)
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Authenticated successfully',
                'token' => $token,
                'user' => $user,
            ], 200);

        } catch (Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production if needed
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            // Find user
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Delete tokens
            $user->tokens()->delete();

            // Delete user
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
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
