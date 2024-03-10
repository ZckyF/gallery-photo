<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Save;
use App\Models\User;
use App\Http\Resources\SaveResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreSaveRequest;
use App\Http\Requests\UpdateSaveRequest;
use App\Http\Resources\Save2Resource;

class SaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Use Gate to check permissions using SavePolicyController
            Gate::authorize('getAll', Save::class);
            // Get user data based on their token
            $user = Auth::user();
            // Fetch data based on user_id
            $data = Save::with(['user:id,username','photo:id,title,file_name'])->where('user_id', $user->id)->get();
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Save Photos data.',
                'data' => SaveResource::collection($data)
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
            $data = Save::with(['user:id,username', 'photo:id,title,file_name'])
                ->where(['photo_id' => $idPhoto])
                ->get();
            // $data = Save::with(['user:id,username', 'photo:id,title,file_name'])
            //     ->where(['photo_id' => $idPhoto])
            //     ->get();
                
        
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Saves data by photo.',
                'data' => SaveResource::collection($data)
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
            $data = Save::with(['user:id,username,avatar_name'])->where('user_id', $user->id)->get();
            // Successful response
            return response()->json([
                'message' => 'Successful in getting Saves data by username',
                'data' => Save2Resource::collection($data)
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
             // Status code 403 indicates unauthorized action.
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
             // Status code 500 indicates a server error.
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaveRequest $request)
    {
        try {
            // Get user data based on their token
            $user = Auth::user();
            // Merge user id
            $request->merge(['user_id' => $user->id, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')]);
            // Create Save 
            $data = Save::create($request->all());
            // Sucessfull response
            return response()->json([
                'message' => 'Save saved successfully.',
                'data' => new SaveResource($data->load(['user:id,username', 'photo:id,title,file_name']))
            ], 201);
        }  catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Save $save)
    {
        //
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaveRequest $request, Save $save)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Save $save)
    {
        try {
            // Use Gate to check permissions using SavePolicy
            Gate::authorize('delete', $save);
            // Delete Like save
            $save->delete();
            // Successfull response
            return response()->json([
                'message' => 'Save deleted successfully.',
                'data' => new SaveResource($save->load(['user:id,username','photo:id,title,file_name']))
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
