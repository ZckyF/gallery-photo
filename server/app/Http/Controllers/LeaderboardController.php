<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeaderboardResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
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
            $users = User::whereIn('id', $leaderboard->pluck('following_id'))
                ->with(['lastThreePhotos'])
                ->get();

            // Transform data menggunakan resource baru
            $leaderboardData = LeaderboardResource::collection($users);

            return response()->json([
                'message' => 'Successfully get Leaderboard by Follower data',
                'data' => $leaderboardData,
            ]);
            
        } catch (\Exception $e) {
            // Status code 500 indicates a server error.
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }
}
