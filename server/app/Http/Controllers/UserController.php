<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['message' => "Halo"]);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function searchUser(string $keyword) 
    {
        try {
            // Cari pengguna berdasarkan kata kunci
            $result = User::with(['latestPhoto'])->whereRaw('LOWER(username) LIKE ?', ["%".strtolower($keyword)."%"])
                    ->get();
    
            // Response sukses
            return response()->json([
                'message' => 'Successfully get Search User data',
                'data' => UserResource::collection($result)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kirimkan respons yang sesuai
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }


    public function leaderboardFollower() 
    {
        try {
            // Menggunakan query builder untuk menghitung jumlah follower untuk setiap user
            $leaderboard = DB::table('follows')
                ->select('following_id', DB::raw('COUNT(follower_id) as follower_count'))
                ->groupBy('following_id')
                ->orderByDesc('follower_count')
                ->get();
    
            // Ambil user berdasarkan id yang ada di $leaderboard
            $users = User::whereIn('id', $leaderboard->pluck('following_id'))->get();
    
            return response()->json([
                'message' => 'Successfully get Leaderboard by Follower data',
                'data' => UserResource::collection($users)
            ]);
            
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username)
    {
        try {
            
            $user = User::where('username', $username)->first();
            // Was the user found ?
            if ($user) {
                // Response sukses
                return response()->json([
                    'message' => 'User found successfully.',
                    'data' => new UserResource($user),
                ], 201);
            } else {
                // Data not found
                return response()->json(['message' => 'Data not found.'], 404);
            }

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            //  Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403); 
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occured'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
           
            // Mengupdate data user
            $user->update($request->all());
    
            // Integrasi metode userImage
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $file = $request->file('file');
                $randomString = Str::random(20);
                $randomId = uniqid();
                $fileName = $randomString . '_' . $randomId . '.' . $file->extension();
    
                // Simpan gambar baru menggunakan Storage::put
                $file->storeAs('public/avatars', $fileName);

    
                // Hapus gambar lama jika ada dan bukan default.jpg
                if ($user->avatar_name && $user->avatar_name !== 'default.jpg') {
                    Storage::delete("public/avatars/{$user->avatar_name}");
                }
                
    
                // Update field 'avatar_name' di database dengan nama file yang benar
                $user->avatar_name = $fileName;
                // Save user
                $user->save();
            }
    
            // Response sukses
            return response()->json([
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occured'], 500);
        }
    }
    
    
    

    public function resetPassword(Request $request)
    {
        // Validation rules for resetting password
        $rules = [
            'newPassword' => 'required|string|min:8',
            'confirmPassword' => 'required|string|min:8|same:newPassword',
        ];
    
        $request->validate($rules);

        // Find the user by reset token
        $user = Auth::user();
    
        
        // find User
        if (!$user) {
            return response()->json(['message' => 'Invalid reset token.'], 400);
        }
    
        // Update the user's password
        $user->update([
            'password' => Hash::make($request->input('newPassword')),
        ]);
    
        return response()->json(['message' => 'Password reset successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
