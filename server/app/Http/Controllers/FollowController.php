<?php

// app/Http/Controllers/FollowController.php

namespace App\Http\Controllers;

use App\Http\Requests\FollowRequest;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\FollowResource;
use Illuminate\Validation\ValidationException;

class FollowController extends Controller
{
    public function indexByUsername(string $username)
    {
        try {
            // Dapatkan data user berdasarkan username
            $user = User::where('username', $username)->first();
    
            if (!$user) {
                // Data not found
                return response()->json(['message' => 'Data not found.'], 404);
            }
    
            // Get data pengikut (followers) dan yang diikuti (following) berdasarkan user ID
            $followers = Follow::with('following:id,username,email,full_name,bio,avatar_name')
                ->where('follower_id', $user->id)
                ->get(['id', 'follower_id', 'following_id', 'created_at']);
            
    
            $following = Follow::with('follower:id,username,email,full_name,address,bio,avatar_name')
                ->where('following_id', $user->id)
                ->get(['id', 'follower_id', 'following_id', 'created_at']);
            
            return response()->json([
                'message' => 'Successful in getting follows data.',
                'data' => [
                    'followers' => FollowResource::collection($followers),
                    'following' => FollowResource::collection($following),
                ],
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 menunjukkan tindakan yang tidak diizinkan.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            // Status code 500 menunjukkan kesalahan server.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
    

    public function store(FollowRequest $request)
    {
        try {
            // Mendapatkan user yang sedang masuk
            $user = Auth::user();
            
            // Tambahkan validasi untuk memastikan pengguna tidak dapat mengikuti dirinya sendiri
            if ($request->follower_id == $user->id) {
                return response()->json([
                    'message' => 'You cannot follow yourself.',
                ], 422);
            }
            
    
            // Check if the follow relationship already exists
            $existingFollow = Follow::where('follower_id', $request['follower_id'])
                ->where('following_id', $user->id)
                ->first();
    
            if ($existingFollow) {
                // Jika relasi sudah ada, kembalikan respon error
                return response()->json([
                    'message' => 'You are already following this user.',
                ], 422);
            }
           
    
            // Update following_id in the validated data
            $request->merge(['following_id' => $user->id]);
            // Buat entitas Follow
            $follow = Follow::create(array_merge($request->all(), ['created_at' => Carbon::now()->format('Y-m-d H:i:s')]));
    
            return response()->json([
                'message' => 'Followed successfully.',
                'data' => new FollowResource($follow->load(['follower', 'following'])),
            ], 201);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    

    public function destroy(string $userIdToUnfollow)
    {
        try {
            // Mendapatkan user yang sedang masuk
            $currentUser = Auth::user();
            
    
            // Cari data follow berdasarkan follower_id (user yang sedang login) dan following_id (user yang ingin di-"unfollow")
            $follow = Follow::where('following_id', $currentUser->id)
                ->where('follower_id', $userIdToUnfollow)
                ->first();
                
            if (!$follow) {
                // Data not found
                return response()->json(['message' => 'Data not found.'], 404);
            }
    
            // Gunakan Gate untuk memeriksa izin menggunakan FollowPolicy
            Gate::authorize('delete', $follow);
            // Hapus follow
            $follow->delete();
    
            // Respon sukses
            return response()->json([
                'message' => 'Follow data deleted successfully.',
                'data' => new FollowResource($follow->load(['follower', 'following'])),
            ], 201);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Kode status 403 menunjukkan tindakan yang tidak diizinkan.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            // Kode status 500 menunjukkan kesalahan server.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function destroyById(string $id) 
    {
        try {
            
            $currentUser = Auth::user();
            // Cari data follow berdasarkan id
            $follow = Follow::where('id', $id)
            ->where('follower_id', $currentUser->id)
            ->first();
            if (!$follow) {
                // Data not found
                return response()->json(['message' => 'Data not found.'], 404);
            }
    
            // Gunakan Gate untuk memeriksa izin menggunakan FollowPolicy
            Gate::authorize('delete', $follow);
            // Hapus follow
            $follow->delete();
            // Respon sukses
            return response()->json([
                'message' => 'Follow data deleted successfully.',
                'data' => new FollowResource($follow->load(['follower', 'following'])),
            ], 201);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Kode status 403 menunjukkan tindakan yang tidak diizinkan.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            // Kode status 500 menunjukkan kesalahan server.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}

