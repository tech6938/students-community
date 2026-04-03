<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{


    public function show()
    {
        try {
            $user = Auth::user();

            // return $user->id;
            $profile = Profile::where('user_id', $user->id)->with('photos')->get();
            // return $profile;

            return ApiResponse::success(
                'Profile retrieved successfully',
                $profile
            );
        } catch (Exception $e) {
            return ApiResponse::error('Profile not found', 404);
        }
    }



    public function store(Request $request)
    {
        try {
            if (auth()->user()->profile) {
                return ApiResponse::error('Profile already exists', 400);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:profiles,username',
                'home_school' => 'nullable|string|max:255',
                'abroad_school' => 'nullable|string|max:255',
                'home_city' => 'nullable|string|max:255',
                'current_city' => 'nullable|string|max:255',
                'languages' => 'nullable|array',
                'interests' => 'nullable|array',
                'images' => 'nullable|array|max:3',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'profile_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors(), 422);
            }

            $data = $validator->validated(); // ✅ FIX

            DB::beginTransaction();

            // ✅ Profile image
            if ($request->hasFile('profile_img')) {
                $file = $request->file('profile_img');
                $filename = time() . '_profile_' . $file->getClientOriginalName();
                $file->move(public_path('photos'), $filename);

                $data['profile_img'] = 'photos/' . $filename;
            }

            // ✅ Create profile
            $data['user_id'] = auth()->id();
            $profile = Profile::create($data);

            auth()->user()->update([
                'profile_status' => 1
            ]);

            // ✅ Multiple images
            if ($request->hasFile('images')) {
                $imageCount = 0;

                foreach ($request->file('images') as $image) {

                    if ($imageCount >= 3) break;

                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('photos'), $filename);

                    $profile->photos()->create([
                        'image' => 'photos/' . $filename
                    ]);

                    $imageCount++;
                }
            }

            DB::commit();

            $profile->load('photos');

            return ApiResponse::success(
                'Profile created successfully',
                $profile,
                201
            );
        } catch (Exception $e) {

            DB::rollBack();

            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $profile = auth()->user()->profile;

            if (!$profile) {
                return ApiResponse::error('Profile not found', 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'home_city' => 'nullable|string|max:255',
                'home_school' => 'nullable|string|max:255',
                'abroad_school' => 'nullable|string|max:255',
                'languages' => 'nullable|array',
                'interests' => 'nullable|array',
                'images' => 'nullable|array|max:3',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'profile_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors(), 422);
            }

            $data = $validator->validated(); // ✅ FIX

            // ❌ username not allowed
            unset($data['username']);

            // ✅ languages update
            if ($request->has('languages')) {
                $existing = $profile->languages ?? [];
                foreach ($request->input('languages') as $index => $value) {
                    $existing[$index] = $value;
                }
                $data['languages'] = array_values($existing);
            }

            // ✅ interests update
            if ($request->has('interests')) {
                $existing = $profile->interests ?? [];
                foreach ($request->input('interests') as $index => $value) {
                    $existing[$index] = $value;
                }
                $data['interests'] = array_values($existing);
            }

            // ✅ update basic data
            $profile->update($data);

            // ✅ images update
            if ($request->hasFile('images')) {

                $existingPhotos = $profile->photos()->get();
                $existingCount = $existingPhotos->count();

                foreach ($request->file('images') as $index => $image) {

                    if (!$image) continue;

                    if (!isset($existingPhotos[$index]) && $existingCount >= 3) {
                        continue;
                    }

                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('photos'), $filename);

                    if (isset($existingPhotos[$index])) {
                        $existingPhotos[$index]->update([
                            'image' => 'photos/' . $filename
                        ]);
                    } else {
                        $existingCount++;
                        $profile->photos()->create([
                            'image' => 'photos/' . $filename
                        ]);
                    }
                }
            }

            $profile->load('photos');

            return ApiResponse::success(
                'Profile updated successfully',
                $profile
            );
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function destroy()
    {
        try {
            $profile = auth()->user()->profile;

            if (!$profile) {
                return ApiResponse::error('Profile not found', 404);
            }

            $profile->delete();

            return ApiResponse::success(
                'Profile deleted successfully'
            );
        } catch (Exception $e) {
            return ApiResponse::error('Profile deletion failed');
        }
    }
}
