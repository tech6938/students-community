<?php

namespace App\Http\Controllers;

use App\Models\Buzz;
use Illuminate\Http\Request;
use Throwable;

class BuzzController extends Controller
{
    // GET /api/buzz/all
    public function index(Request $request)
    {
        try {
            $buzzes = Buzz::where('user_id', $request->user()->id)->get();

            return response()->json([
                'status' => true,
                'data' => $buzzes
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/buzz/show/{id}
    public function show(Request $request, $id)
    {
        try {
            $buzz = Buzz::where('id', $id)
                        ->where('user_id', $request->user()->id)
                        ->first();

            if (!$buzz) {
                return response()->json([
                    'status' => false,
                    'message' => 'Buzz not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $buzz
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/buzz/create
    public function store(Request $request)
    {
        try {
            $request->validate([
                'location'      => 'required|string',
                'place'         => 'required|string',
                'buzz_type'     => 'required|string',
                'tags'          => 'nullable|array',
                'beelo_mission' => 'boolean',
                'rating'        => 'required|numeric|min:0|max:5',
                'img'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'desc'          => 'nullable|string',
            ]);

            $imgPath = null;
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('buzzes'), $filename);
                $imgPath = url('buzzes/' . $filename);
            }

            $buzz = Buzz::create([
                'user_id'       => $request->user()->id,
                'location'      => $request->location,
                'place'         => $request->place,
                'buzz_type'     => $request->buzz_type,
                'tags'          => $request->tags,
                'beelo_mission' => $request->beelo_mission ?? false,
                'rating'        => $request->rating,
                'img'           => $imgPath,
                'desc'          => $request->desc,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Buzz created successfully',
                'data' => $buzz
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/buzz/update/{id}
    public function update(Request $request, $id)
    {
        try {
            $buzz = Buzz::where('id', $id)
                        ->where('user_id', $request->user()->id)
                        ->first();

            if (!$buzz) {
                return response()->json([
                    'status' => false,
                    'message' => 'Buzz not found'
                ], 404);
            }

            $request->validate([
                'location'      => 'sometimes|string',
                'place'         => 'sometimes|string',
                'buzz_type'     => 'sometimes|string',
                'tags'          => 'nullable|array',
                'beelo_mission' => 'boolean',
                'rating'        => 'sometimes|numeric|min:0|max:5',
                'img'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'desc'          => 'nullable|string',
            ]);

            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('buzzes'), $filename);
                $buzz->img = url('buzzes/' . $filename);
            }

            $buzz->update($request->except('img'));

            return response()->json([
                'status' => true,
                'message' => 'Buzz updated successfully',
                'data' => $buzz
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/buzz/delete/{id}
    public function destroy(Request $request, $id)
    {
        try {
            $buzz = Buzz::where('id', $id)
                        ->where('user_id', $request->user()->id)
                        ->first();

            if (!$buzz) {
                return response()->json([
                    'status' => false,
                    'message' => 'Buzz not found'
                ], 404);
            }

            $buzz->delete();

            return response()->json([
                'status' => true,
                'message' => 'Buzz deleted successfully'
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
