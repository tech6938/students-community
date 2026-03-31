<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

return response()->json([
    'message' => 'Journal created successfully',
    'data' => $journal
]);    }

    // 📖 READ (ALL)
    public function index()
    {
        $journals = Journal::where('user_id', auth()->id())->get();

        return response()->json($journals);
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

        return response()->json($journal);
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

return response()->json([
    'message' => 'Journal updated successfully',
    'data' => $journal
]);    }


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

        return response()->json(['message' => 'Deleted successfully']);
    }

}
