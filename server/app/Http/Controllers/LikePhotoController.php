<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LikePhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\LikePhotoResource;
use App\Http\Requests\StoreLikePhotoRequest;
use App\Http\Requests\UpdateLikePhotoRequest;
use Illuminate\Http\Request;

class LikePhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Use Gate to check permissions using LikePhotoPolicy
            Gate::authorize('getAll', LikePhoto::class);
            // Get user data based on their token
            $user = Auth::user();
            // Fetch data based on user_id
            $data = LikePhoto::with(['user:id,username','photo:id,title,file_name'])->where('user_id', $user->id)->get();
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Like Photos data.',
                'data' => LikePhotoResource::collection($data)
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
             // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
             // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function indexByIdPhoto(string $idPhoto)
    {
        try {
            
           
            // Fetch data based on user_id and folder_id
            $data = LikePhoto::with(['user:id,username', 'photo:id,title,file_name'])
                ->where(['photo_id' => $idPhoto])
                ->get();
                
        
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Like Photos data by photo.',
                'data' => LikePhotoResource::collection($data)
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function indexByUsername(string $username)
    {
        try {
             // Dapatkan data user berdasarkan username
             $user = User::where('username', $username)->first();
             if(!$user) {
                // Data not found
                return response()->json(['message' => 'Data not found.'], 404);
             }
            // Fetch data based on user_id
            $data = LikePhoto::with(['user:id,username,avatar_name'])->where('user_id', $user->id)->get();
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Like Photos data by username',
                'data' => LikePhotoResource::collection($data)
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
             // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
             // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
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

    /**
     * Display the specified resource.
     */
    public function show(LikePhoto $likePhoto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLikePhotoRequest $request, LikePhoto $likePhoto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LikePhoto $likePhoto)
    {
        try {
            // Use Gate to check permissions using LikePhotoPolicy
            Gate::authorize('delete', $likePhoto);
            // Delete Like Photo
            $likePhoto->delete();
            // Successfull response
            return response()->json([
                'message' => 'Like Photo deleted successfully.',
                'data' => new LikePhotoResource($likePhoto->load(['user:id,username','photo:id,title']))
            ]);
        } 
        catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403); 
        }  catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
