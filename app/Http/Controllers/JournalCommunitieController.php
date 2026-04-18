<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\JournalCommunitie;
use Illuminate\Http\Request;

class JournalCommunitieController extends Controller
{

    public function index()
    {
        $userId = auth()->id();

        $journalCommunities = JournalCommunitie::where('user_id', $userId)
            ->with(['community', 'hiveboard', 'story'])
            ->get();

        $data = $journalCommunities->map(function ($item) {
            return [
                'type' => $item->type,
                'community' => $item->community,
                'hiveboard' => $item->hiveboard,
                'story' => $item->story,
            ];
        });

        return ApiResponse::success(
            'Communities fetched successfully',
            $data,
            200
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'communities_id' => 'nullable|exists:communities,id',
            'hiveboards_id'  => 'nullable|exists:hive_boards,id',
            'stories_id'     => 'nullable|exists:stories,id',
            'type'           => 'string|required',
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['message' => 'User not logged in'], 401);
        }

        $data = JournalCommunitie::firstOrCreate([
            'user_id'        => $userId,
            'communities_id' => $validated['communities_id'] ?? null,
            'hiveboards_id'  => $validated['hiveboards_id'] ?? null,
            'stories_id'     => $validated['stories_id'] ?? null,
            'type'           => $validated['type'],
        ]);

        if (!$data->wasRecentlyCreated) {
            return ApiResponse::error(
                'This item has already been added to your journal',
                409
            );
        }

        return ApiResponse::success(
            'Added to Journal successfully',
            $data,
            201
        );
    }


    public function destroy($type, $id)
    {
        $userId = auth()->id();

        if (!in_array($type, ['community', 'hiveboard', 'story'])) {
            return ApiResponse::error('Invalid type', 422);
        }

        $column = match ($type) {
            'community' => 'communities_id',
            'hiveboard' => 'hiveboards_id',
            'story'     => 'stories_id',
        };

        $deleted = JournalCommunitie::where($column, $id)
            ->where('type', $type)
            ->where('user_id', $userId)
            ->delete();

        if (!$deleted) {
            return ApiResponse::error(
                ucfirst($type) . ' not found in journal',
                404
            );
        }

        return ApiResponse::success(
            ucfirst($type) . ' removed successfully',
            [],
            200
        );
    }
}
