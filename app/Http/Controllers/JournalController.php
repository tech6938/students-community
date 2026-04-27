<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Buzz;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class JournalController extends Controller
{

    // CREATE
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'category' => 'required|integer',
    //         'title'    => 'required',
    //         'place'    => 'required',
    //         'rating'   => 'required|numeric',
    //         'notes'    => 'required',
    //         'file'     => 'nullable|image|mimes:jpg,jpeg,png',
    //         'video'    => 'nullable|mimes:mp4,mov,avi'
    //     ]);

    //     $data = $request->only([
    //         'category',
    //         'title',
    //         'place',
    //         'rating',
    //         'notes'
    //     ]);

    //     // 🖼️ Handle Image
    //     if ($request->hasFile('file')) {
    //         $imagePath = $request->file('file')->store('images', 'public');
    //         $data['file'] = $imagePath;
    //     }

    //     // 🎥 Handle Video
    //     if ($request->hasFile('video')) {
    //         $videoPath = $request->file('video')->store('videos', 'public');
    //         $data['video'] = $videoPath;
    //     }

    //     $data['user_id'] = auth()->id();

    //     $journal = Journal::create($data);

    //     return ApiResponse::success(
    //         'Journal created successfully',
    //         $journal,
    //         201
    //     );
    // }

    public function store(Request $request)
    {
        try {
            // Base validation for journal
            $validated = $request->validate([
                'category' => 'required|integer',
                'title'    => 'required|string',
                'place'    => 'required|string',
                'lng'      => 'nullable',
                'lat'      => 'nullable',
                'rating'   => 'required|numeric|min:0|max:5',
                'notes'    => 'required|string',
                'file'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'video'    => 'nullable|mimes:mp4,mov,avi|max:51200',
                'link_to_buzz' => 'sometimes|boolean|in:0,1', // 0 or 1
            ]);

            // If link_to_buzz is 1, validate buzz fields
            $linkToBuzz = $request->input('link_to_buzz', 0);

            // if ($linkToBuzz == 1) {
            //     $buzzValidation = $request->validate([
            //         'location'      => 'required|string',
            //         'place'        => 'required|string',
            //         'buzz_type'     => 'required|string',
            //         'tags'          => 'nullable|array',
            //         'beelo_mission' => 'boolean',
            //         'buzz_rating'   => 'required|numeric|min:0|max:5', // Renamed to avoid conflict
            //         'img'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            //         'desc'     => 'nullable|string',
            //         'coords'     => 'nullable|string',
            //     ]);
            // }

            // DB::beginTransaction();

            // Prepare journal data
            $journalData = $request->only([
                'category',
                'title',
                'place',
                'lng',
                'lat',
                'rating',
                'notes'
            ]);

            // Handle Journal Image
            if ($request->hasFile('file')) {
                $imagePath = $request->file('file')->store('images', 'public');
                $journalData['file'] = $imagePath;
            }

            // Handle Journal Video
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('videos', 'public');
                $journalData['video'] = $videoPath;
            }

            $journalData['user_id'] = auth()->id();
            // $journalData['link_to_buzz'] = $linkToBuzz;

            // // Create Buzz if link_to_buzz is 1
            // if ($linkToBuzz == 1) {
            //     // Handle Buzz Image
            //     $buzzImgPath = null;
            //     if ($request->hasFile('buzz_img')) {
            //         $buzzFile = $request->file('buzz_img');
            //         $buzzFilename = time() . '_buzz_' . $buzzFile->getClientOriginalName();
            //         $buzzFile->move(public_path('buzzes'), $buzzFilename);
            //         $buzzImgPath = 'buzzes/' . $buzzFilename;
            //     }

            //     // Create Buzz
            //     $buzz = Buzz::create([
            //         'user_id'       => auth()->id(),
            //         'location'      => $request->location,
            //         'coords'        => $request->coords,
            //         'place'         => $request->place, // Using journal's place
            //         'buzz_type'     => $request->buzz_type,
            //         'tags'          => $request->tags ?? [],
            //         'beelo_mission' => $request->beelo_mission ?? false,
            //         'rating'        => $request->buzz_rating,
            //         'img'           => $buzzImgPath,
            //         'desc'          => $request->buzz_desc,
            //     ]);

            //     // Link buzz to journal
            //     $journalData['buzz_id'] = $buzz->id;
            // }

            // Create Journal
            $journal = Journal::create($journalData);

            // Load buzz relationship if exists
            if ($linkToBuzz == 1 && isset($buzz)) {
                $journal->load('buzz');
            }

            DB::commit();

            return ApiResponse::success(
                $linkToBuzz == 1 ? 'Journal and Buzz created successfully' : 'Journal created successfully',
                $journal,
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
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
        try {
            // Find journal or fail
            $journal = Journal::findOrFail($id);

            // Check authorization (only owner can update)
            if ($journal->user_id !== auth()->id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: You can only update your own journals'
                ], 403);
            }

            // Base validation for journal
            $validated = $request->validate([
                'category' => 'sometimes|required|integer',
                'title'    => 'sometimes|required|string',
                'place'    => 'sometimes|required|string',
                'lng'      => 'sometimes|nullable',
                'lat'      => 'sometimes|nullable',
                'rating'   => 'sometimes|required|numeric|min:0|max:5',
                'notes'    => 'sometimes|required|string',
                'file'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'video'    => 'nullable|mimes:mp4,mov,avi|max:51200',
                'link_to_buzz' => 'sometimes|boolean|in:0,1',
            ]);

            // Get current link_to_buzz value (existing or from request)
            // $currentLinkToBuzz = $journal->link_to_buzz;
            // $newLinkToBuzz = $request->input('link_to_buzz', $currentLinkToBuzz);

            // // If link_to_buzz is 1, validate buzz fields
            // if ($newLinkToBuzz == 1) {
            //     $buzzValidation = $request->validate([
            //         'location'      => 'sometimes|required|string',
            //         'place'         => 'sometimes|required|string',
            //         'buzz_type'     => 'sometimes|required|string',
            //         'tags'          => 'nullable|array',
            //         'beelo_mission' => 'boolean',
            //         'buzz_rating'   => 'sometimes|required|numeric|min:0|max:5',
            //         'img'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            //         'desc'          => 'nullable|string',
            //         'coords'        => 'nullable|string',
            //     ]);
            // }

            // DB::beginTransaction();

            // Prepare journal data
            $journalData = $request->only([
                'category',
                'title',
                'lng',
                'lat',
                'place',
                'rating',
                'notes'
            ]);

            // Handle Journal Image Update
            if ($request->hasFile('file')) {
                // Delete old image if exists
                if ($journal->file && Storage::disk('public')->exists($journal->file)) {
                    Storage::disk('public')->delete($journal->file);
                }
                $imagePath = $request->file('file')->store('images', 'public');
                $journalData['file'] = $imagePath;
            }

            // Handle Journal Video Update
            if ($request->hasFile('video')) {
                // Delete old video if exists
                if ($journal->video && Storage::disk('public')->exists($journal->video)) {
                    Storage::disk('public')->delete($journal->video);
                }
                $videoPath = $request->file('video')->store('videos', 'public');
                $journalData['video'] = $videoPath;
            }

            // $journalData['link_to_buzz'] = $newLinkToBuzz;

            // // Handle Buzz logic based on link_to_buzz value
            // if ($newLinkToBuzz == 1) {
            //     // CASE 1: Create new buzz or update existing
            //     if ($currentLinkToBuzz == 0 || is_null($journal->buzz_id)) {
            //         // Create new buzz (was 0, now 1)
            //         $buzzImgPath = null;
            //         if ($request->hasFile('img')) {
            //             $buzzFile = $request->file('img');
            //             $buzzFilename = time() . '_buzz_' . $buzzFile->getClientOriginalName();
            //             $buzzFile->move(public_path('buzzes'), $buzzFilename);
            //             $buzzImgPath = 'buzzes/' . $buzzFilename;
            //         } elseif ($request->input('existing_img')) {
            //             // Keep existing image if provided
            //             $buzzImgPath = $request->input('existing_img');
            //         }

            //         $buzz = Buzz::create([
            //             'user_id'       => auth()->id(),
            //             'location'      => $request->location ?? $journal->place,
            //             'coords'        => $request->coords,
            //             'place'         => $request->place ?? $journal->place,
            //             'buzz_type'     => $request->buzz_type,
            //             'tags'          => $request->tags ?? [],
            //             'beelo_mission' => $request->beelo_mission ?? false,
            //             'rating'        => $request->buzz_rating,
            //             'img'           => $buzzImgPath,
            //             'desc'          => $request->desc,
            //         ]);

            //         $journalData['buzz_id'] = $buzz->id;
            //     } else {
            //         // Update existing buzz
            //         $buzz = Buzz::findOrFail($journal->buzz_id);

            //         // Update buzz fields if provided
            //         $buzzData = [];

            //         if ($request->has('location')) $buzzData['location'] = $request->location;
            //         if ($request->has('coords')) $buzzData['coords'] = $request->coords;
            //         if ($request->has('place')) $buzzData['place'] = $request->place;
            //         if ($request->has('buzz_type')) $buzzData['buzz_type'] = $request->buzz_type;
            //         if ($request->has('tags')) $buzzData['tags'] = $request->tags;
            //         if ($request->has('beelo_mission')) $buzzData['beelo_mission'] = $request->beelo_mission;
            //         if ($request->has('buzz_rating')) $buzzData['rating'] = $request->buzz_rating;
            //         if ($request->has('desc')) $buzzData['desc'] = $request->desc;

            //         // Handle buzz image update
            //         if ($request->hasFile('img')) {
            //             // Delete old image if exists
            //             if ($buzz->img && file_exists(public_path($buzz->img))) {
            //                 unlink(public_path($buzz->img));
            //             }
            //             $buzzFile = $request->file('img');
            //             $buzzFilename = time() . '_buzz_' . $buzzFile->getClientOriginalName();
            //             $buzzFile->move(public_path('buzzes'), $buzzFilename);
            //             $buzzData['img'] = 'buzzes/' . $buzzFilename;
            //         }

            //         if (!empty($buzzData)) {
            //             $buzz->update($buzzData);
            //         }
            //     }
            // } elseif ($newLinkToBuzz == 0 && $currentLinkToBuzz == 1 && $journal->buzz_id) {
            //     // CASE 2: Remove buzz link (was 1, now 0)
            //     // Option 1: Delete the associated buzz
            //     $buzz = Buzz::find($journal->buzz_id);
            //     if ($buzz) {
            //         // Delete buzz image if exists
            //         if ($buzz->img && file_exists(public_path($buzz->img))) {
            //             unlink(public_path($buzz->img));
            //         }
            //         $buzz->delete();
            //     }
            //     $journalData['buzz_id'] = null;
            // }

            // Update journal
            $journal->update($journalData);

            // // Load buzz relationship if exists
            // if ($newLinkToBuzz == 1 && isset($buzz)) {
            //     $journal->load('buzz');
            // }

            // DB::commit();

            // $message = "";
            // if ($newLinkToBuzz == 1) {
            //     if ($currentLinkToBuzz == 0) {
            //         $message = "Journal and Buzz created successfully";
            //     } else {
            //         $message = "Journal and Buzz updated successfully";
            //     }
            // } else {
            //     if ($currentLinkToBuzz == 1) {
            //         $message = "Journal updated and Buzz removed successfully";
            //     } else {
            //         $message = "Journal updated successfully";
            //     }
            // }
            $message = "Journal updated successfully";

            return ApiResponse::success($message, $journal);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Journal not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
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
