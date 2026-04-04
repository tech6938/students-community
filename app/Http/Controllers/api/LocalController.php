<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LocalController extends Controller
{
public function getLocal()
    {
        try {
            $users = User::with(['profile:id,user_id,profile_img,name,abroad_school,home_city,current_city,username'])
                ->where('id', '!=', auth()->id())
                ->whereHas('profile')
                ->get()
                ->map(function ($user) {
                    return $user->profile;
                });

            return response()->json([
                'status' => true,
                'message' => "All Locals Retrieved Successfully",
                'data' => $users,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
