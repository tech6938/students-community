<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\HiveBoard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class HiveBoardController extends Controller
{
    /**
     * Display a listing of HiveBoards.
     */
    public function index(): JsonResponse
    {
        try {
            $hiveBoards = HiveBoard::all();

            return ApiResponse::success(
                'HiveBoards retrieved successfully',
                $hiveBoards,
                201
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created HiveBoard.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'place' => 'required|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'desc' => 'required|string',
                'post_as' => 'required|string|max:255',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'link_to_journal' => 'nullable|boolean',
                'event_date' => 'nullable',
            ]);

            // ✅ Attach logged-in user
            $validated['user_id'] = $request->user()->id;

            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('hive_files', 'public');
                $validated['file'] = $filePath;
            }

            $validated['link_to_journal'] = $validated['link_to_journal'] ?? false;

            $hiveBoard = HiveBoard::create($validated);

            return ApiResponse::success(
                'Profile created successfully',
                $hiveBoard,
                201
            );
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified HiveBoard.
     */
    public function show($id): JsonResponse
    {
        try {
            $hiveBoard = HiveBoard::findOrFail($id);
            return ApiResponse::success(
                'HiveBoard retrieved successfully',
                $hiveBoard,
                201
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'HiveBoard not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified HiveBoard.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $hiveBoard = HiveBoard::findOrFail($id);

            // ✅ Only owner can update
            if ($hiveBoard->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'place' => 'sometimes|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'desc' => 'sometimes|string',
                'post_as' => 'sometimes|string|max:255',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'link_to_journal' => 'nullable|boolean',
                'event_date' => 'nullable',
            ]);

            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('hive_files', 'public');
                $validated['file'] = $filePath;
            }

            $hiveBoard->update($validated);
            return ApiResponse::success(
                'HiveBoard updated successfully',
                $hiveBoard,
                201
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'HiveBoard not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified HiveBoard.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $hiveBoard = HiveBoard::findOrFail($id);

            // ✅ Only owner can delete
            if ($hiveBoard->user_id !== request()->user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $hiveBoard->delete();

            return response()->json([
                'status' => true,
                'message' => 'HiveBoard deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'HiveBoard not found'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
