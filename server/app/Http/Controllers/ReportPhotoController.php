<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ReportPhoto;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreReportPhotoRequest;
use App\Http\Requests\UpdateReportPhotoRequest;
use App\Http\Resources\ReportPhotoResource;

class ReportPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportPhotoRequest $request)
    {
        try {
            // Get user data based on their token
            $user = Auth::user();
            // Merge user id and created_at
            $request->merge(['user_id' => $user->id, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')]);
            // Create Save 
            $data = ReportPhoto::create($request->all());
            // Sucessfull response
            return response()->json([
                'message' => 'Report photo send successfully.',
                'data' => new ReportPhotoResource($data->load(['user:id,username','photo:id,title,file_name'])) 
            ], 201);
        }  catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportPhoto $reportPhoto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportPhotoRequest $request, ReportPhoto $reportPhoto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportPhoto $reportPhoto)
    {
        //
    }
}
