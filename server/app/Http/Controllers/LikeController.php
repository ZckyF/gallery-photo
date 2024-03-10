<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLikePhotoRequest;
use App\Models\LikePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store(StoreLikePhotoRequest $request)
    {
        try {
            return response()->json(['message' => 'halo.']);
            // Get user data based on their token
            $user = Auth::user();
                // Check if the user has already liked the photo
            $existingLike = LikePhoto::where('user_id', $user->id)
            ->where('photo_id', $request->photo_id)
            ->first();

            if ($existingLike) {
                // User has already liked the photo
                return response()->json(['message' => 'You have already liked this photo.'], 422);
            }
            // Merge user id and created_at
            $request->merge(['user_id' => $user->id,'created_at' => Carbon::now()->format('Y-m-d H:i:s')]);
            
            // Create LikePhoto 
            $data = LikePhoto::create($request->all());
            // Sucessfull response
            return response()->json([
                'message' => 'Like Photo saved successfully.',
                'data' => new LikePhotoResource($data->load(['user:id,username','photo:id,title'])) 
            ], 201);
        }  catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
