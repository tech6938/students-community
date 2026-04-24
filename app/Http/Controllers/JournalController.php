<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ApiResponse;

class JournalController extends Controller
{

    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|integer',
            'title'    => 'required',
            'place'    => 'required',
            'rating'   => 'required|numeric',
            'notes'    => 'required',
            'file'     => 'nullable|image|mimes:jpg,jpeg,png',
            'video'    => 'nullable|mimes:mp4,mov,avi'
        ]);

        $data = $request->only([
            'category',
            'title',
            'place',
            'rating',
            'notes'
        ]);

        // 🖼️ Handle Image
        if ($request->hasFile('file')) {
            $imagePath = $request->file('file')->store('images', 'public');
            $data['file'] = $imagePath;
        }

        // 🎥 Handle Video
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
            $data['video'] = $videoPath;
        }

        $data['user_id'] = auth()->id();

        $journal = Journal::create($data);

        return ApiResponse::success(
            'Journal created successfully',
            $journal,
            201
        );
    }

    // READ (ALL)
    public function index()
    {
        $journals = Journal::all();

        // return response()->json($journals);
        return ApiResponse::success(
            'All journals retrived successfully',
            $journals,
            201
        );
    }

    // READ (ONE)
    public function show($id)
    {
        $journal = Journal::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$journal) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return ApiResponse::success(
            'Journal retrived successfully',
            $journal,
            201
        );
    }

    public function updateJournal(Request $request, $id)
    {
        $request->validate([
            'category' => 'sometimes|required|integer',
            'title'    => 'sometimes|required',
            'place'    => 'sometimes|required',
            'rating'   => 'sometimes|required|numeric',
            'notes'    => 'sometimes|required',
            'file'     => 'nullable|image|mimes:jpg,jpeg,png',
            'video'    => 'nullable|mimes:mp4,mov,avi'
        ]);

        $loggedInUser = auth()->user();

        // Only owner can update their own journal
        $journal = Journal::where('user_id', $loggedInUser->id)
            ->where('id', $id)
            ->first();

        if (!$journal) {
            return ApiResponse::error('Journal not found or you are not authorized to update it', 404);
        }

        $data = $request->only([
            'category',
            'title',
            'place',
            'rating',
            'notes'
        ]);

        // 🖼️ Handle Image Update
        if ($request->hasFile('file')) {
            // Delete old image if exists
            if ($journal->file) {
                Storage::disk('public')->delete($journal->file);
            }

            $imagePath = $request->file('file')->store('images', 'public');
            $data['file'] = $imagePath;
        }

        // 🎥 Handle Video Update
        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($journal->video) {
                Storage::disk('public')->delete($journal->video);
            }

            $videoPath = $request->file('video')->store('videos', 'public');
            $data['video'] = $videoPath;
        }

        // Update the journal
        $journal->update($data);

        return ApiResponse::success(
            'Journal updated successfully',
            $journal,
            200
        );
    }

    // READ By Category
    public function getByCate($id)
    {
        $journal = Journal::where('category', $id)
            ->get();

        if (!$journal) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return ApiResponse::success(
            'Journal retrived By Category',
            $journal,
            201
        );
    }

    // ✏️ UPDATE
    public function update(Request $request, $id)
    {
        $journal = Journal::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$journal) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $request->validate([
            'category' => 'sometimes|integer',
            'title' => 'sometimes',
            'place' => 'sometimes',
            'rating' => 'sometimes|numeric',
            'notes' => 'sometimes',
            'img' => 'sometimes|image'
        ]);

        $data = $request->only([
            'category',
            'title',
            'place',
            'rating',
            'notes'
        ]);

        if ($request->hasFile('img')) {

            if ($journal->img) {
                Storage::disk('public')->delete($journal->img);
            }

            $imagePath = $request->file('img')->store('images', 'public');
            $data['img'] = $imagePath;
        }

        $journal->update($data);

        return ApiResponse::success(
            'Journal updated successfully',
            $journal,
            201
        );
    }


    public function destroy($id)
    {
        $loggedInUser = auth()->user();
        $isAdmin = ($loggedInUser->user_type === 'admin');

        if ($isAdmin) {
            // Admin can delete any journal
            $journal = Journal::find($id);
        } else {
            // Normal user can only delete their own journal
            $journal = Journal::where('user_id', $loggedInUser->id)
                ->where('id', $id)
                ->first();
        }

        if (!$journal) {
            return response()->json(['message' => 'Journal not found'], 404);
        }

        // Optional: Log admin deletion for audit
        if ($isAdmin && $journal->user_id !== $loggedInUser->id) {
        }

        // Delete image if exists
        if ($journal->img) {
            Storage::disk('public')->delete($journal->img);
        }

        $journal->delete();

        $message = ($isAdmin && $journal->user_id !== $loggedInUser->id)
            ? 'Journal deleted successfully by admin'
            : 'Journal deleted successfully';

        return ApiResponse::success($message);
    }
}
