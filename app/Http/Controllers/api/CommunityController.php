<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Community;
use Exception;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    // GET /api/communities
    public function index()
    {
        try {
            $data = Community::latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Communities fetched successfuly',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/communities
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'place' => 'required|string|max:255',
                'caption' => 'nullable|string',
                'post_as' => 'required|string|max:255',
                'link_to_journal' => 'boolean'
            ]);

            // ✅ attach logged-in user
            $validated['user_id'] = $request->user()->id;

            if ($request->hasFile('img')) {
                $validated['img'] = $request->file('img')->store('communities', 'public');
            }

            $community = Community::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Community created successfully',
                'data' => $community
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create community',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/communities/{id}
    public function show($id)
    {
        try {
            $community = Community::findOrFail($id);

            return ApiResponse::success(
                'Community fetched successfully',
                $community,
                200
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Community not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // PUT/PATCH /api/communities/{id}
    public function update(Request $request, $id)
    {
        try {
            $community = Community::findOrFail($id);

            // ✅ Optional: ensure user owns this record
            if ($community->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validated = $request->validate([
                'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'place' => 'sometimes|required|string|max:255',
                'caption' => 'nullable|string',
                'post_as' => 'sometimes|required|string|max:255',
                'link_to_journal' => 'boolean'
            ]);

            if ($request->hasFile('img')) {
                $validated['img'] = $request->file('img')->store('communities', 'public');
            }

            $community->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Community updated successfully',
                'data' => $community
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update community',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/communities/{id}
    public function destroy($id)
    {
        try {
            $community = Community::findOrFail($id);

            // ✅ Only owner can delete
            if ($community->user_id !== request()->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $community->delete();

            return response()->json([
                'success' => true,
                'message' => 'Community deleted successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete community',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
