<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Generate 6-digit OTP
        $otp = random_int(100000, 999999);

        // Save OTP (2-3 minutes expiry)
        Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(3)
        ]);

        // Send OTP email
        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('OTP Verification');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email',
        ], 200);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);

        // Find OTP record
        $otpRecord = Otp::where('otp', $request->otp)->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 400);
        }

        // Check expiry
        if (Carbon::now()->gt($otpRecord->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired',
                'data' => null
            ], 400);
        }

        // Get email from OTP record
        $email = $otpRecord->email;

        // Find or create user
        $user = User::firstOrCreate([
            'email' => $email
        ]);

        // Delete OTP after use
        $otpRecord->delete();

        // Generate token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => "User login successfully",
            'token' => $token,
            'data' => $user,
        ]);
    }


    public function logout(Request $request)
    {
        // Delete current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    // public function auth(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'email' => 'required|email'
    //         ]);

    //         // Check user
    //         $user = User::where('email', $request->email)->first();

    //         $isNewUser = false;

    //         if (!$user) {
    //             // REGISTER
    //             $user = User::create([
    //                 'email' => $request->email,
    //                 'profile_status' => 0,
    //             ]);

    //             $isNewUser = true;
    //         }

    //         // LOGIN (token)
    //         $token = $user->createToken('api-token')->plainTextToken;

    //         return response()->json([
    //             'status' => true,
    //             'message' => $isNewUser
    //                 ? 'User registered successfully'
    //                 : 'User login successfully',
    //             'token' => $token,
    //             'user' => $user,
    //         ], 200);
    //     } catch (Throwable $e) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage() // remove in production
    //         ], 500);
    //     }
    // }

    // public function logout(Request $request)
    // {
    //     try {
    //         $request->user()->currentAccessToken()->delete();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Logged out successfully'
    //         ], 200);
    //     } catch (Throwable $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
