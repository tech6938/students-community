<?php

namespace App\Http\Controllers;

use App\Mail\AdminReportNotificationMail;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                'message' => 'Provide exactly one target (community, hiveboard, story, or journal)'
            ], 422);
        }

        // 🔥 Match type with correct field
        $expectedColumn = match ($validated['type']) {
            'community' => 'community_id',
            'hiveboard' => 'hiveboards_id',
            'story'     => 'stories_id',
            'journal'   => 'journal_id',
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
            'journal_id'    => $validated['journal_id'] ?? null,
            'issue'         => $validated['issue'],
            'description'   => $validated['description'] ?? null,
        ]);

        try {
            $admins = User::where('user_type', 'admin')->get();

            if ($admins->isNotEmpty()) {
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new AdminReportNotificationMail($report, $admin));
                }

                Log::info('Admin notification emails sent to ' . $admins->count() . ' admins');
            } else {
                Log::warning('No admin users found to send report notification');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification emails: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'Report submitted successfully.',
            'data' => $report
        ], 201);
    }
}
