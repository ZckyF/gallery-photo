<?php

namespace App\Http\Controllers;

use App\Models\CommentPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreCommentPhotoRequest;
use App\Http\Requests\UpdateCommentPhotoRequest;
use App\Http\Resources\CommentPhotoResource;
use Carbon\Carbon;

class CommentPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Use Gate to check permissions using SavePolicyController
            Gate::authorize('getAll', CommentPhoto::class);
            // Get user data based on their token
            $user = Auth::user();
            // Fetch data based on user_id
            $data = CommentPhoto::with(['user:id,username,created_at','photo:id,title,file_name','parent:id,user_id,replies:id,comment,photo_id,user_id'])->where('user_id', $user->id)->get();
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Comment data.',
                'data' => CommentPhotoResource::collection($data)
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
            $data = CommentPhoto::with(['user:id,username,avatar_name', 'photo:id,title,file_name'])
                ->where(['photo_id' => $idPhoto])
                ->get();
                
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Comments data by photo.',
                'data' => CommentPhotoResource::collection($data)
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
    public function store(StoreCommentPhotoRequest $request)
    {
        try {
            // Get user data based on their token
            $user = Auth::user();
            // Merge user id and created_at
            $request->merge(['user_id' => $user->id,'created_at' => Carbon::now()->format('Y-m-d H:i:s')]);
            // Create Folder 
            $data = CommentPhoto::create($request->all());
            // Sucessfull response
            return response()->json([
                'message' => 'Comment saved successfully.',
                'data' => new CommentPhotoResource($data->load(['user:id,username','photo:id,title,file_name','parent:id,user_id'])) 
            ], 201);
        }  catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CommentPhoto $commentPhoto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentPhotoRequest $request, CommentPhoto $commentPhoto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommentPhoto $commentPhoto)
    {
        try {
            // Use Gate to check permissions using LikePhotoPolicy
            Gate::authorize('delete', $commentPhoto);

            
            // Check if the comment has replies
            if (!$commentPhoto->parent_id && $commentPhoto->replies()->count() > 0) {
                // Delete the comment and its replies recursively

                // Delete the comment
                $commentPhoto->delete();

                // Delete all replies recursively
                foreach ($commentPhoto->replies as $reply) {
                    $reply->delete();
                }
            } else {
                // Delete the comment
                $commentPhoto->delete();
            }
            // Successfull response
            return response()->json([
                'message' => 'Like Photo deleted successfully.',
                'data' => new CommentPhotoResource($commentPhoto->load(['user:id,username','photo:id,title,file_name','parent:id,user_id']))
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
