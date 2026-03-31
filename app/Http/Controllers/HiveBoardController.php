<?php

namespace App\Http\Controllers;

use App\Models\HiveBoard;
use Beste\Json;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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

            return response()->json([
                'success' => true,
                'message' => 'HiveBoards retrieved successfully',
                'data' => $hiveBoards,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
                'user_id' => 'required|integer',
                'title' => 'required|string|max:255',
                'place' => 'required|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'desc' => 'required|string',
                'post_as' => 'required|string|max:255',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'link_to_journal' => 'nullable|boolean',
            ]);

            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('hive_files', 'public');
                $validated['file'] = $filePath;
            }

            $validated['link_to_journal'] = $validated['link_to_journal'] ?? false;

            $hiveBoard = HiveBoard::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'HiveBoard created successfully',
                'data' => $hiveBoard,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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

            return response()->json([
                'success' => true,
                'message' => 'HiveBoard retrieved successfully',
                'data' => $hiveBoard,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'HiveBoard not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'place' => 'sometimes|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'desc' => 'sometimes|string',
                'post_as' => 'sometimes|string|max:255',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'link_to_journal' => 'nullable|boolean',
            ]);
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('hive_files', 'public');
                $validated['file'] = $filePath;
            }

            $hiveBoard->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'HiveBoard updated successfully',
                'data' => $hiveBoard,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'HiveBoard not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
            $hiveBoard->delete();

            return response()->json([
                'success' => true,
                'message' => 'HiveBoard deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'HiveBoard not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}