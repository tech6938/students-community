<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Buzz;
use App\Models\BuzzRating;
use Illuminate\Http\Request;
use Throwable;

class BuzzController extends Controller
{
    // GET /api/buzz/all
    public function index()
    {
        try {
            $buzzes = Buzz::all();

            return ApiResponse::success(
                'All buzzes retrived successfully',
                $buzzes
            );
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/buzz/show/{id}
    // public function show($id)
    // {
    //     try {
    //         $buzz = Buzz::where('id', $id)
    //             ->first();

    //         if (!$buzz) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Buzz not found'
    //             ], 404);
    //         }

    //         return ApiResponse::success(
    //             'Buzz retrived successfully',
    //             $buzz
    //         );
    //     } catch (Throwable $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function show($id)
    {
        try {
            $buzz = Buzz::with([
                'user.profile:id,user_id,username',
                'ratings.user.profile:id,user_id,username'
            ])->find($id);

            if (!$buzz) {
                return response()->json([
                    'status' => false,
                    'message' => 'Buzz not found'
                ], 404);
            }

            $data = [
                'id' => $buzz->id,
                'location' => $buzz->location,
                'place' => $buzz->place,
                'buzz_type' => $buzz->buzz_type,
                'tags' => $buzz->tags,
                'beelo_mission' => $buzz->beelo_mission,
                'rating' => $buzz->rating,
                'img' => $buzz->img,
                'desc' => $buzz->desc,

                // ✅ username from profile
                'username' => optional($buzz->user->profile)->username,

                // Ratings
                'ratings' => $buzz->ratings->map(function ($rating) {
                    return [
                        'id' => $rating->id,
                        'flag' => $rating->flag,
                        'rating' => $rating->rating,
                        'tags' => $rating->tags,
                        'img_url' => $rating->img_url,
                        'desc' => $rating->desc,

                        // ✅ username from profile
                        'username' => optional($rating->user->profile)->username,
                    ];
                })
            ];

            return ApiResponse::success(
                'Buzz retrieved successfully',
                $data
            );
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
                'coords'        => 'required|string',
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
                $imgPath = 'buzzes/' . $filename;
            }

            $buzz = Buzz::create([
                'user_id'       => $request->user()->id,
                'location'      => $request->location,
                'coords'        => $request->coords,
                'place'         => $request->place,
                'buzz_type'     => $request->buzz_type,
                'tags'          => $request->tags,
                'beelo_mission' => $request->beelo_mission ?? false,
                'rating'        => $request->rating,
                'img'           => $imgPath,
                'desc'          => $request->desc,
            ]);

            return ApiResponse::success(
                'Buzz created successfully',
                $buzz
            );
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
                'coords'          => 'nullable|string',
            ]);

            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('buzzes'), $filename);
                $buzz->img = 'buzzes/' . $filename;
            }

            $buzz->update($request->except('img'));

            return ApiResponse::success(
                'Buzz updated successfully',
                $buzz
            );
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

            return ApiResponse::success(
                'Buzz deleted successfully',
                $buzz
            );
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ------------------------------------------------BUZZ RATING----------------------------


    public function ratingsIndex()
    {
        try {
            $ratings = \App\Models\BuzzRating::all();

            return ApiResponse::success(
                'Buzz ratings fetched successfully',
                $ratings
            );
        } catch (Throwable $e) {
            return ApiResponse::error('Something went wrong', 500, $e->getMessage());
        }
    }


    public function ratingsShow($id)
    {
        try {
            $rating = \App\Models\BuzzRating::where('id', $id)->first();

            if (!$rating) {
                return ApiResponse::error('Buzz rating not found', 404);
            }

            return ApiResponse::success(
                'Buzz rating fetched successfully',
                $rating
            );
        } catch (Throwable $e) {
            return ApiResponse::error('Something went wrong', 500, $e->getMessage());
        }
    }

    public function ratingsStore(Request $request)
    {
        try {
            $request->validate([
                'buzzes_id' => 'required|exists:buzzes,id',
                'flag'    => 'required|in:1,2',
                'rating'  => 'required|numeric|min:0|max:5',
                'tags'    => 'nullable|array',
                'img'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'desc'    => 'nullable|string',
            ]);

            $imgPath = null;

            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('buzz_ratings'), $filename);
                $imgPath = 'buzz_ratings/' . $filename;
            }

            $rating = \App\Models\BuzzRating::create([
                'buzzes_id' => $request->buzzes_id,
                'user_id'   => auth()->id(),
                'flag'    => $request->flag,
                'rating'  => $request->rating,
                'tags'    => $request->tags,
                'img'     => $imgPath,
                'desc'    => $request->desc,
            ]);

            return ApiResponse::success(
                'Buzz rating created successfully',
                $rating
            );
        } catch (Throwable $e) {
            return ApiResponse::error('Something went wrong', 500, $e->getMessage());
        }
    }

    public function ratingsUpdate(Request $request, $id)
    {
        try {
            $rating = BuzzRating::where('id', $id)->first();

            if (!$rating) {
                return ApiResponse::error('Buzz rating not found', 404);
            }

            $request->validate([
                'flag'   => 'sometimes|in:1,2',
                'rating' => 'sometimes|numeric|min:0|max:5',
                'tags'   => 'nullable|array',
                'img'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'desc'   => 'nullable|string',
            ]);

            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('buzz_ratings'), $filename);
                $rating->img = 'buzz_ratings/' . $filename;
            }

            $rating->update($request->except('img'));

            return ApiResponse::success(
                'Buzz rating updated successfully',
                $rating
            );
        } catch (Throwable $e) {
            return ApiResponse::error('Something went wrong', 500, $e->getMessage());
        }
    }

    public function ratingsDestroy($id)
    {
        try {
            $rating = \App\Models\BuzzRating::where('id', $id)->first();

            if (!$rating) {
                return ApiResponse::error('Buzz rating not found', 404);
            }

            $rating->delete();

            return ApiResponse::success(
                'Buzz rating deleted successfully',
            );
        } catch (Throwable $e) {
            return ApiResponse::error('Something went wrong', 500, $e->getMessage());
        }
    }
}
