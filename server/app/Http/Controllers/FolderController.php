<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\FolderResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Use Gate to check permissions using ProductPolicy
            Gate::authorize('getAll', Folder::class);
            // Get user data based on their token
            $user = Auth::user();
            // Fetch data based on user_id
            $data = Folder::with(['user:id,username'])->where('user_id', $user->id)->get();
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Folders data.',
                'data' => FolderResource::collection($data)
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403); // Status code 403 indicates unauthorized action.
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function indexByUsername(string $username)
    {
        try {
            // Get data user by username
            $user = User::where('username', $username)->first();
    
            if(!$user) {
                 // Data not found
                 return response()->json(['message' => 'Data not found.'], 404);
            }
    
            // Fetch data based on user_id
            $data = Folder::with(['user:id,username','photos:id,folder_id,file_name'])
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($folder) {
                    $folder->photos = $folder->photos->take(4);
                    return $folder;
                });
    
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Folders data.',
                'data' => FolderResource::collection($data),
            ]);
    
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403); // Status code 403 indicates unauthorized action.
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFolderRequest $request)
    {
        try {
            // Get user data based on their token
            $user = Auth::user();
            // Merge user id
            $request->merge(['user_id' => $user->id]);
            // Create Folder 
            $data = Folder::create($request->all());
            // Sucessfull response
            return response()->json([
                'message' => 'Folder saved successfully.',
                'data' => new FolderResource($data->load(['user:id,username'])) 
            ], 201);
        }  catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function searchFolder(string $keyword) 
    {
        try {
            // Sebagai contoh, saya menggunakan LIKE untuk mencari gambar berdasarkan nama file
            $result = Folder::whereRaw('LOWER(folder_name) LIKE ?', ["%".strtolower($keyword)."%"])
            ->orWhereRaw('LOWER(description) LIKE ?', ["%".strtolower($keyword)."%"])
            ->get();
            // Response sukses
            return response()->json([
                'message' => 'Successfully get Search Photo data',
                'data' => FolderResource::collection($result->load(['user:id,username,avatar_name', 'photos:id,folder_id,title,file_name,user_id','photos.user:id,username,avatar_name']))
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kirimkan respons yang sesuai
            return response()->json(['message' => $e->getMessage()], 422);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $usernameAndFolderName)
    {
        try {
            
            list($username, $folderName) = explode('--', $usernameAndFolderName, 2);
            
            // Menggantikan tanda hubung (-) dengan karakter kosong ('')
            $folderName = str_replace('-', ' ', $folderName);
            
            
            // Cari folder berdasarkan username dan folder_name
            $folder = Folder::whereHas('user', function ($query) use ($username) {
                $query->where('username', $username);
            })->where('folder_name', $folderName)->with(['user:id,username,avatar_name', 'photos:id,folder_id,title,file_name,user_id','photos.user:id,username,avatar_name'])->first();

        
            if (!$folder) {
                // Folder tidak ditemukan
                return response()->json(['message' => 'Folder not found.'], 404);
            }
            // Load semua foto yang terkait dengan folder
            // $folder->load(['user:id,username', 'photos:id,folder_id,file_name,user_id']);
            // Sukses response
            return response()->json([
                'message' => 'Folder found successfully.',
                'data' => new FolderResource($folder),
            ], 201);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
    }
    
    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        try {
            // Get user data based on their token
            $user = Auth::user();
            // Merge user id
            $request->merge(['user_id' => $user->id]);
            // Updated
            $folder->update($request->all());
            // Get data recent after refresh
            $data = $folder->refresh();
            // Successfull response
            return response()->json([
                'message' => 'Folder updated successfully.',
                'data' => new FolderResource($data->load(['user:id,username']))
            ]);
        }  catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403); 
        }  catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        try {
            // Use Gate to check permissions using ProductPolicy
            Gate::authorize('delete', $folder);
    
            // Hapus semua foto di dalam folder
            foreach ($folder->photos as $photo) {
                // Hapus file dari penyimpanan
               
                Storage::disk('public')->delete("photos/{$photo->file_name}");
    
                // Hapus foto dari database
                $photo->delete();
            }
    
            // Hapus folder
            $folder->delete();
    
            // Successfull response
            return response()->json([
                'message' => 'Folder deleted successfully.',
                'data' => new FolderResource($folder->load(['user:id,username']))
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
