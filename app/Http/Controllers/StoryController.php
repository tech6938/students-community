<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Story;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    // 📌 CREATE
    public function store(Request $request)
    {
        $request->validate([
            'post_type' => 'required|integer',
            'title' => 'required',
            'desc' => 'required',
            'place' => 'nullable',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'post_as' => 'required',
            'link_to_journal' => 'nullable|boolean'
        ]);

        $data = $request->only([
            'post_type',
            'title',
            'desc',
            'place',
            'tags',
            'post_as',
            'link_to_journal'
        ]);


        if ($request->has('tags') && is_string($request->tags)) {  // ADD THIS
            $data['tags'] = json_decode($request->tags, true);      // ADD THIS
        }

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('stories', 'public');
        }

        $data['user_id'] = auth()->id();

        $story = Story::create($data);

        return response()->json([
            'message' => 'Story created successfully',
            'data' => $story
        ]);
    }

    // 📖 GET ALL
    public function index()
    {
        try {
            $stories = Story::all();

            return ApiResponse::success(
                'Story retrieved successfully',
                $stories
            );
        } catch (Exception $e) {
            return ApiResponse::error('Story not found', 404);
        }
    }

    // 🔍 GET ONE
    public function show($id)
    {
        try {
            $story = Story::where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$story) {
                return response()->json(['message' => 'Not found'], 404);
            }

            return ApiResponse::success(
                'Stories retrieved successfully',
                $story
            );
        } catch (Exception $e) {
            return ApiResponse::error('Profile not found', 404);
        }
    }

    // ✏️ UPDATE
    public function update(Request $request, $id)
    {
        try {
            $story = Story::where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$story) {
                return response()->json(['message' => 'Not found'], 404);
            }

            $request->validate([
                'post_type' => 'sometimes|integer',
                'title' => 'sometimes',
                'desc' => 'sometimes',
                'place' => 'sometimes',
                'tags' => 'sometimes|array',
                'tags.*' => 'string',
                'img' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
                'post_as' => 'sometimes',
                'link_to_journal' => 'sometimes|boolean'
            ]);

            $data = $request->only([
                'post_type',
                'title',
                'desc',
                'place',
                'tags',
                'post_as',
                'link_to_journal'
            ]);

            if ($request->hasFile('img')) {
                if ($story->img) {
                    Storage::disk('public')->delete($story->img);
                }

                $data['img'] = $request->file('img')->store('stories', 'public');
            }

            $story->update($data);

            return ApiResponse::success(
                'Stories retrieved successfully',
                $story
            );
        } catch (Exception $e) {
            return ApiResponse::error('Story not found', 404);
        }
    }

    // ❌ DELETE
    public function destroy($id)
    {
        try {
            $story = Story::where('user_id', auth()->id())
                ->where('id', $id)
                ->first();

            if (!$story) {
                return response()->json(['message' => 'Not found'], 404);
            }

            if ($story->img) {
                Storage::disk('public')->delete($story->img);
            }

            $story->delete();

            return ApiResponse::success(
                'Stories retrieved successfully',
                $story
            );
        } catch (Exception $e) {
            return ApiResponse::error('Story not found', 404);
        }
    }
}
