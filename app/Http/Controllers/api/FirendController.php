<?php

namespace App\Http\Controllers\api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirendController extends Controller
{
    public function addFriend(Request $request)
    {
        try {
            // Validate the receiver_id
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
            ]);

            // Prevent sending friend request to self
            if ($request->receiver_id == Auth::id()) {
                return ApiResponse::badRequest(
                    'You cannot send a friend request to yourself.',
                );
            }

            // Optional: Check if friend request already exists
            $existing = Friend::where('sender_id', Auth::id())
                ->where('receiver_id', $request->receiver_id)
                ->first();

            if ($existing) {
                return ApiResponse::badRequest(
                    'Friend request already sent.',
                );
            }

            // Create friend request
            $friend = Friend::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'accepted' => false,
            ]);

            return ApiResponse::success(
                'Friend request sent successfully.',
                $friend
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function incomingRequests()
    {
        try {
            // Get all friend requests where the authenticated user is the receiver and not yet accepted
            $requests = Friend::with('sender') // optional: include sender's user info
                ->where('receiver_id', Auth::id())
                ->where('accepted', false)
                ->get();

            return ApiResponse::success(
                'ncoming friend requests retrieved successfully.',
                $requests
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function friendList()
    {
        try {
            $userId = Auth::id();

            $friends = Friend::where('sender_id', $userId)
                ->with([
                    'receiver.profile:id,user_id,profile_img,name,abroad_school,home_city,current_city,username'
                ])
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'All friends retrieved successfully.',
                'data' => $friends,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function acceptFriend(Request $request)
    {
        try {
            // Validate the request id
            $request->validate([
                'friend_id' => 'required|exists:friends,id',
            ]);

            // Find the friend request
            $friendRequest = Friend::where('id', $request->friend_id)
                ->where('receiver_id', Auth::id())
                ->where('accepted', false)
                ->first();

            if (!$friendRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Friend request not found or already accepted.',
                ], 404);
            }

            // Accept the friend request
            $friendRequest->accepted = true;
            $friendRequest->save();

            return ApiResponse::success(
                'Friend request accepted successfully.',
                $friendRequest
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}
