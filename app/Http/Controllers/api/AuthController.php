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
            'otp' => 'required',
        ]);

        $otpRecord = Otp::where('otp', $request->otp)->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 400);
        }

        if (Carbon::now()->gt($otpRecord->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired',
            ], 400);
        }

        $email = trim($otpRecord->email);

        // Decide user type
        $userType = ($email === "26beelo@gmail.com") ? 'admin' : 'user';

        $user = User::firstOrCreate(
            ['email' => $email],
            ['user_type' => $userType]
        );

        $otpRecord->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        // Convert user to array and add profile_status
        $userData = $user->toArray();
        $userData['profile_status'] = $user->profile_status ?? 0;

        return response()->json([
            'status' => true,
            'message' => "User login successfully",
            'token' => $token,
            'data' => $userData,
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
}
