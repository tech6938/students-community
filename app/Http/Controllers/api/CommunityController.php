<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;
use Exception;

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
                'user_id' => 'required',
                'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'place' => 'required|string|max:255',
                'caption' => 'nullable|string',
                'post_as' => 'required|string|max:255',
                'link_to_journal' => 'boolean'
            ]);

            if ($request->hasFile('img')) {
                $validated['img'] = $request->file('img')->store('communities', 'public');
            }

            $community = Community::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Community created successfully',
                'data' => $community
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create community',
                'error' => $e->getMessage()
            ]);
        }
    }

    // GET /api/communities/{id}
    public function show($id)
    {
        try {
            $community = Community::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $community
            ]);

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

            $validated = $request->validate([
                'user_id' => 'sometimes|required',
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

        } catch (Exception $e) {
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
            $community->delete();

            return response()->json([
                'success' => true,
                'message' => 'Community deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete community',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}