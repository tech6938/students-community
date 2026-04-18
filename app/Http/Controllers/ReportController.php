<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validated = $request->validate([
            'type' => 'required|in:community,hiveboard,story,journal',
            'community_id'  => 'nullable|exists:communities,id',
            'hiveboards_id' => 'nullable|exists:hive_boards,id',
            'stories_id'    => 'nullable|exists:stories,id',
            'journal_id'    => 'nullable|exists:journals,id',

            'issue' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // 🔥 Ensure only ONE target is provided
        $targets = [
            'community_id'  => $validated['community_id'] ?? null,
            'hiveboards_id' => $validated['hiveboards_id'] ?? null,
            'stories_id'    => $validated['stories_id'] ?? null,
            'journal_id'    => $validated['journal_id'] ?? null,
        ];

        $filled = array_filter($targets);

        if (count($filled) !== 1) {
            return response()->json([
                'status' => false,
                'message' => 'Provide exactly one target (community, hiveboard, or story)'
            ], 422);
        }

        // 🔥 Match type with correct field
        $expectedColumn = match ($validated['type']) {
            'community' => 'community_id',
            'hiveboard' => 'hiveboards_id',
            'story'     => 'stories_id',
            'journal'     => 'journal_id',
        };

        if (empty($validated[$expectedColumn])) {
            return response()->json([
                'status' => false,
                'message' => 'Type does not match provided ID'
            ], 422);
        }

        $report = Report::create([
            'user_id'       => $userId,
            'type'          => $validated['type'],
            'community_id'  => $validated['community_id'] ?? null,
            'hiveboards_id' => $validated['hiveboards_id'] ?? null,
            'stories_id'    => $validated['stories_id'] ?? null,
            'journal_id' => $validated['journal_id'] ?? null,
            'issue'         => $validated['issue'],
            'description'   => $validated['description'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Report submitted successfully',
            'data' => $report
        ], 201);
    }
}
