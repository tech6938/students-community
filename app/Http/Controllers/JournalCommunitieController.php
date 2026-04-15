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
            ->with('community')
            ->get();

        // Extract only the community data from each record
        $data = $journalCommunities->pluck('community');

        return ApiResponse::success(
            'Communities fetched successfully',
            $data,
            201
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'communities_id' => 'required|exists:communities,id',
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['message' => 'User not logged in'], 401);
        }

        $data = JournalCommunitie::firstOrCreate([
            'user_id' => $userId,
            'communities_id' => $validated['communities_id']
        ]);

        if (!$data->wasRecentlyCreated) {
            return ApiResponse::error(
                'This community has already been added to your journal',
                409
            );
        }

        return ApiResponse::success(
            'Community added to Journal successfully',
            $data,
            201
        );
    }

    public function destroy($id)
    {
        $data = JournalCommunitie::where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Communnity not found in journal'], 401);
        }
        $data->delete();

        return ApiResponse::success(
            'Communnity removed from Journal successfully',
            201
        );
    }
}
