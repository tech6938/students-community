<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ApiResponse;

class JournalController extends Controller
{

    // ✅ CREATE (you already have this)
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|integer',
            'title' => 'required',
            'place' => 'required',
            'rating' => 'required|numeric',
            'notes' => 'required',
            'img' => 'nullable|image'
        ]);

        $data = $request->only([
            'category',
            'title',
            'place',
            'rating',
            'notes'
        ]);

        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')->store('images', 'public');
            $data['img'] = $imagePath;
        }

        $data['user_id'] = auth()->id();

        $journal = Journal::create($data);

        return ApiResponse::success(
            'Journal created successfully',
            $journal,
            201
        );
    }

    // 📖 READ (ALL)
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

    // 🔍 READ (ONE)
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

        // 🔍 READ By Category
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
        $journal = Journal::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$journal) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // delete image AFTER check
        if ($journal->img) {
            Storage::disk('public')->delete($journal->img);
        }

        $journal->delete();

        return ApiResponse::success(
            'Journal deleted successfully',
            201
        );
    }
}
