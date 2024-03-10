<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class NewPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            // Check if the email exists in the database
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Email not found'], 404);
            }
            // Generate a unique token
            $token = Str::random(60);;
    
            // Save the token in your 'password_reset_tokens' table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => $token, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')]
            );
            
            // Kirim email konfirmasi
            $this->sendPasswordResetConfirmation($user->email);
            // Send the token in the response
            return response()->json([
                'message' => 'Reset password link sent to your email.',
                'data' => [
                    'token' => $token
                ] 
                ]);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    protected function sendPasswordResetConfirmation($email)
    {
        $resetConfirmationUrl = 'http://localhost:3000/reset-password';

        Mail::send('emails.password_reset_confirmation', ['resetConfirmationUrl' => $resetConfirmationUrl], function ($mail) use ($email) {
            $mail->to($email)->subject('Password Reset Confirmation');
        });
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'newPassword' => 'required|string|min:8',
                'confirmPassword' => 'required|string|same:newPassword',
            ]);
            
            // Periksa token dalam tabel password_reset_tokens
            $tokenData = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->first();
            
                
            if (!$tokenData) {
                return response()->json(['message' => 'Invalid or expired token.'], 422);
            }
    
            // Verifikasi token dan atur ulang password jika sesuai
            $user = User::where('email', $tokenData->email)->first();
            $user->update(['password' => Hash::make($request->newPassword)]);
    
            // Hapus token setelah digunakan
            DB::table('password_reset_tokens')->where('token', $request->token)->delete();
    
            return response()->json(['message' => 'Password reset successfully.']);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
