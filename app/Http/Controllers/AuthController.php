<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{


public function auth(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    // Check user
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        // REGISTER
        $user = User::create([
            'email' => $request->email
        ]);
    }

    // LOGIN (token)
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token
    ]);
}

public function delete(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    // Find user
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // Delete all tokens (important)
    $user->tokens()->delete();

    // Delete user
    $user->delete();

    return response()->json([
        'message' => 'User deleted successfully'
    ]);
}

}
