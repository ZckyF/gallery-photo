<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Otp;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{

    public function login(LoginRequest $request) 
    {
        try  {
            // return response()->json(['message' => 'halo']);
            // apakah username atau email ada di database
            $user = User::where('email', $request->usernameOrEmail)
            ->orWhere('username', $request->usernameOrEmail)
            ->firstOrFail();
            // Periksa kata sandi
            if (!Hash::check($request->password, $user->password)) { 
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            // Get the OTP for the user
            $otp = Otp::where('user_id', $user->id)
            ->where('otp_verified', true)
            ->latest() // Order by created_at in descending order
            ->first();


            // Check if the OTP is valid
            if (!$otp) {
                // Generate unique OTP with 6 digits
                do {
                    $otp = rand(100000, 999999);
                } while (Otp::where('otp', $otp)->exists());
                // Set expiration time for OTP (30 minutes from now)
                $otpExpiration = now()->addMinutes(30);
                // Save Otp in the otp table
                Otp::create([
                    'user_id' => $user->id,
                    'otp' => $otp,
                    'expires_at' => $otpExpiration,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
                $userEmail = $user->email;
                $subject = 'OTP Code for Verification';
                $otpCode = $otp;
                Mail::send('emails.otp', ['otpCode' => $otpCode], function ($mail) use ($userEmail, $subject) {
                    $mail->to($userEmail)->subject($subject);
                });

                return response()->json([
                    'message' => 'OTP expired.You have to send otp'
                ], 422);
            }



            // Buat token autentikasi
            $token = $user->createToken('user login')->plainTextToken;
            // Response sukses
            return response()->json([
                'message' => 'Login successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
            ]);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // 401 adalah status kode untuk "Not Found"
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401); 
        }
        catch (ValidationException $e) {
            // 401 adalah status kode untuk "Not Found"
            return response()->json([
                'message' => $e->getMessage(),
            ], 401); 
        }
        catch (\Exception $e) {
            // Tangani pengecualian umum
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    public function join(Request $request)
    {
        try {
            // Validate OTP
            $rules = [
                'username' => 'required|string|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'confirmPassword' => 'required|string|same:password',
            ];
            
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // Validate the request
            if ($validator->fails()) {
                // Jika validasi gagal, kembalikan pesan kesalahan
                return response()->json(['message' => $validator->errors()->first()], 422);
            }
    
            // Generate unique OTP with 6 digits
            do {
                $otp = rand(100000, 999999);
            } while (Otp::where('otp', $otp)->exists());
    
            // Set expiration time for OTP (30 minutes from now)
            $otpExpiration = now()->addMinutes(30);
    
            // Save OTP in the otp table without associating it with any user for now
            $otpData = [
                'otp' => $otp,
                'expires_at' => $otpExpiration,
                'otp_verified' => false,
                'created_at' => Carbon::now()->format('Y-m-d H:i;s')
            ];
            $otp = Otp::create($otpData);
    
            // Send OTP to the user's email
            $userEmail = $request->email;
            $subject = 'OTP Code for Verification';
            $otpCode = $otpData['otp'];
            Mail::send('emails.otp', ['otpCode' => $otpCode], function ($mail) use ($userEmail, $subject) {
                $mail->to($userEmail)->subject($subject);
            });
    
            return response()->json(['message' => 'OTP successfully sent to the user\'s email, please check your email']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
    
    public function verifyOtpJoin(RegisterRequest $request)
    {
        try {   
            // Get OTP from the request
            $otp = $request->otp; 
            // Get all valid OTPs from the database
            $validOtps = Otp::where('expires_at', '>', now())
                        ->where('otp_verified', false) 
                        ->latest()
                        ->get();
    
            // Check if the provided OTP matches any of the valid OTPs
            $matchedOtp = $validOtps->firstWhere('otp', $otp);
    
            // If OTP is not valid
            if (!$matchedOtp) {
                return response()->json(['message' => 'Invalid OTP.'], 422);
            }

    
            // Create the user
            $userData = $request->except('otp', 'confirmPassword');
            $userData['avatar_name'] = 'default.jpg';
            $userData['is_actived'] = true;
            $user = User::create($userData);

            // Update OTP status to verified
            $matchedOtp->otp_verified = true;
            $matchedOtp->user_id = $user->id;
            $matchedOtp->update();
            // Assign role
            $user->assignRole('client');
    
            // Buat token autentikasi
            $token = $user->createToken('user login')->plainTextToken;
    
            // Return success response
            return response()->json([
                'message' => 'OTP verified successfully. User created.',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    

    public function verifyOtp(Request $request)
    {
        try {
            // Validate OTP
            $this->validate($request, [
                'otp' => 'required|string|min:6|max:6',
            ], [
                'otp.max' => 'OTP should not exceed 6 characters.',
            ]);
    
            // Get OTP from the request
            $otp = $request->otp;
    
            // Get all valid OTPs from the database
            $validOtps = Otp::where('expires_at', '>', now())
                        ->where('otp_verified', false) 
                        ->latest()
                        ->get();
            
            
            // Check if the provided OTP matches any of the valid OTPs
            $matchedOtp = $validOtps->firstWhere('otp', $otp);

            

            // If OTP is not valid
            if (!$matchedOtp) {
                return response()->json(['message' => 'Invalid OTP.'], 422);
            }

            $user = $matchedOtp->user;
            
            // Update OTP status to verified
            $matchedOtp->otp_verified = true;
            $matchedOtp->update();

            // Buat token autentikasi
            $token = $user->createToken('user login')->plainTextToken;
    
            // Optional: Delete the OTP record if you don't need it anymore
            // $matchedOtp->delete();
    
            // Return success response
            return response()->json([
                'message' => 'OTP verified successfully.',
                'data' => [
                  'user' => new UserResource($user),
                  'token' =>  $token
                ]
            ], 200);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    


    public function resendOtp(Request $request)
    {
        try {
           
            // Check if the username or email exists in the database
            $user = User::where('email', $request->usernameOrEmail)
                ->orWhere('username', $request->usernameOrEmail)
                ->firstOrFail();
    
            // Check if there is an existing unexpired OTP for the user
            $existingOtp = Otp::where('user_id', $user->id)
                ->where('expires_at', '>', now())
                ->where('otp_verified', false)
                ->first();
    
            // If there is an existing OTP, return an error response
            if ($existingOtp) {
                return response()->json([
                    'message' => 'An unexpired OTP already exists for the user.'
                ], 422);
            }
    
            // Generate a new OTP
            $otp = rand(100000, 999999);
            $otpExpiration = now()->addMinutes(30);
    
            // Save the new OTP
            Otp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'expires_at' => $otpExpiration,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    
            // Send the OTP via email
            $userEmail = $user->email;
            $subject = 'OTP Code for Verification';
    
            // Kirim OTP ke email pengguna
            Mail::send('emails.otp', ['otpCode' => $otp], function ($mail) use ($userEmail, $subject) {
                $mail->to($userEmail)->subject($subject);
            });
    
            // Success response
            return response()->json([
                'message' => 'OTP sent successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // 401 is the status code for "Not Found"
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'message' => 'An error occurred.'
            ], 500);
        }
    }
    
    

    public function logout()
    {
        try {
            return response()->json(['message' => 'An error occurred.'], 500);
            // Ambil pengguna yang saat ini terautentikasi dan hapus semua token
            $data = Auth::user();
            // Hapus Token
            $data->tokens()->delete();
            // Balasan
            return response()->json(['message' => 'Logout successfully.','data' => new UserResource($data)]);
        } 
        catch (\Exception $e) {
            // Tangani pengecualian umum
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
