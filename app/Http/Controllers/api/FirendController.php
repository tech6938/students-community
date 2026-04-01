<?php

namespace App\Http\Controllers\api;

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
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot send a friend request to yourself.',
                ], 400);
            }

            // Optional: Check if friend request already exists
            $existing = Friend::where('sender_id', Auth::id())
                ->where('receiver_id', $request->receiver_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => false,
                    'message' => 'Friend request already sent.',
                ], 400);
            }

            // Create friend request
            $friend = Friend::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'accepted' => false,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Friend request sent successfully.',
                'data' => $friend,
            ], 201);
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

            return response()->json([
                'status' => true,
                'message' => 'Incoming friend requests retrieved successfully.',
                'data' => $requests,
            ], 200);
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
            return $userId;
            $requests = Friend::where('sender_id', $userId)->get();

            return response()->json([
                'status' => true,
                'message' => 'All friends retrieved successfully.',
                'data' => $requests,
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
                ->where('receiver_id', Auth::id()) // ensure the auth user is the receiver
                ->where('accepted', false)        // ensure it's not already accepted
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

            return response()->json([
                'status' => true,
                'message' => 'Friend request accepted successfully.',
                'data' => $friendRequest,
            ], 200);
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
