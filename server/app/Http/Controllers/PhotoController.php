<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Photo;
use App\Models\Follow;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\PhotoResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdatePhotoRequest;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            $data = Photo::with(['user:id,username,avatar_name','folder:id,folder_name'])->where('user_id', $user->id)->get();
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Photos data.',
                'data' => PhotoResource::collection($data)
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
             // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
             // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function randomPhoto()
    {
        try {
            // Fetch a random photo without considering user_id
            $randomPhoto = Photo::with(['user:id,username,avatar_name'])->get();
    
            if (!$randomPhoto) {
                // Jika tidak ada foto yang ditemukan, berikan respons sesuai.
                return response()->json(['message' => 'No random photo found.'], 404);
            }
    
            // Successful response
            return response()->json([
                'message' => 'Successfully get random photos data',
                'data' =>  PhotoResource::collection($randomPhoto),
            ]);
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function randomPhotoHero() 
    {
        try {
            // Mendapatkan satu foto acak dari tabel photos
            $randomPhoto = Photo::with(['user:id,username,avatar_name'])->inRandomOrder()->first();
    
            if ($randomPhoto) {
                return response()->json([
                    'message' => 'Random photo retrieved successfully.',
                    'data' => new PhotoResource($randomPhoto)
                ]);
            } else {
                return response()->json(['message' => 'No photos available.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function randomPhotoFollowing()
    {
        try {
            // Mendapatkan pengguna saat ini
            $user = Auth::user();
    
            // Mendapatkan ID pengguna yang diikuti oleh pengguna saat ini
            $followingIds = Follow::where('following_id', $user->id)->pluck('follower_id');
    
            // Mendapatkan satu foto acak dari pengguna yang diikuti
            $randomPhotos = Photo::with(['user:id,username,avatar_name'])->whereIn('user_id', $followingIds)->inRandomOrder()->get();
    
            if ($randomPhotos->isNotEmpty()) {
                return response()->json([
                    'message' => 'Random photo from following retrieved successfully.',
                    'data' => PhotoResource::collection($randomPhotos)
                ]);
            } else {
                return response()->json(['message' => 'No photos from following available.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePhotoRequest $request)
    {
        try{
            // Get user data based on their token
            $user = Auth::user();
            // Get the file from the request
            $file = $request->file('file');

            // Generate a unique filename
            $randomString = Str::random(20);
            $randomId = uniqid();
            $fileName = $randomString . '_' . $randomId . '.' . $file->extension();

            // Store the file in the 'photos' directory within the storage folder
            $file->storeAs('photos', $fileName, 'public');
            // Merge user_id dan file_name
            $request->merge(['user_id' => $user->id,'file_name' => $fileName]);
            // Create a new photo
            $data = Photo::create($request->all());
            // Sucessfull response
            return response()->json([
                'message' => 'Photo uploaded successfully.', 
                'data' => new PhotoResource($data->load(['user:id,username','folder:id,folder_name']))
            ]);
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Photo $photo)
    // {
    //     try {
    //         dd($photo);
    //         // Use Gate to check permissions using ProductPolicy
    //         Gate::authorize('detail', $photo);
    //         // Sucessfull response
    //         return response()->json([
    //             'message' => 'Photo found successfully.',
    //             'data' => new PhotoResource($photo->load(['user:id,username','folder:id,folder_name','likes:id,photo_id,user_id','comments:id,photo_id,comment,parent_id,created_at'])) 
    //         ], 201);
    //     }  catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    //         // Status code 403 indicates unauthorized action.
    //         return response()->json(['message' => $e->getMessage()], 403); 
    //     }  catch (\Exception $e) {
    //         // Status code 500 indicates a server error.
    //          return response()->json(['message' => 'An error occurred.'], 500);
    //      }
    // }

    public function show($titleAndFileName)
    {
        try {
            // Split title and file_name using '--'
            list($title, $fileName) = explode('--', $titleAndFileName, 2);
    
            // Ganti tanda '-' dengan spasi pada judul
            $title = str_replace('-', ' ', $title);
 
            // Jika ekstensi tidak ada, cari file_name tanpa memperhitungkan ekstensi
            $photo = Photo::where('title', $title)
                    ->where('file_name', 'LIKE', "$fileName%")
                    ->first();

            if (!$photo) {
                return response()->json(['message' => 'Photo not found.'], 404);
            }
    
            // Successful response
            return response()->json([
                'message' => 'Photo found successfully.',
                'data' => new PhotoResource($photo->load(['user:id,username,avatar_name','folder:id,folder_name','saves:id,photo_id,user_id','comments:id,user_id,photo_id,comment,parent_id,created_at'])) 
            ], 201);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    // public function similiarPhoto($titleAndDesciption)
    // {
    //     return 'halo';
    // }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePhotoRequest $request, Photo $photo)
    {
        try {
            // Gunakan data yang sudah divalidasi dari UpdatePhotoRequest
            $validatedData = $request->validated();
            
            // Perbarui data foto berdasarkan ID yang diterima
            $photo->update($validatedData);

            // Kirim respons berhasil
            return response()->json(['message' => 'Photo updated successfully', 'data' => new PhotoResource($photo)]);

        } catch (\Exception $e) {
            // Kirim respons gagal
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function searchPhoto(string $keyword) 
    {
        try {
    
            // Sebagai contoh, saya menggunakan LIKE untuk mencari gambar berdasarkan nama file
            $result = Photo::whereRaw('LOWER(title) LIKE ?', ["%".strtolower($keyword)."%"])
                ->orWhereRaw('LOWER(description) LIKE ?', ["%".strtolower($keyword)."%"])
                ->get();
    
            // Response sukses
            return response()->json([
                'message' => 'Successfully get Search Photo data',
                'data' => PhotoResource::collection($result->load(['user:id,username,avatar_name','folder:id,folder_name']))
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kirimkan respons yang sesuai
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Tangkap error lainnya
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        try {
            // Check if the authenticated user has the right to delete the photo
            $this->authorize('delete', $photo);
            
            $fileName = $photo->file_name;
            // Delete the photo file from storage
            // Make sure the file is not the default.jpg before deleting
            if ($fileName !== 'default.jpg') {
                // Delete the photo file from storage
                Storage::disk('public')->delete("photos/{$fileName}");
            }
    
            // Delete the photo record from the database
            $photo->delete();
    
            return response()->json([
                'message' => 'Photo deleted successfully.',
                'data' => new PhotoResource($photo->load(['user:id,username','folder:id,folder_name']))
        ]);
        }  catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403); 
        }  catch (\Exception $e) {
            
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
    
}
