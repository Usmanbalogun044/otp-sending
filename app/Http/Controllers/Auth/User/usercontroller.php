<?php

namespace App\Http\Controllers\auth\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class usercontroller extends Controller
{
    public function showregistrationform(){
        return view('auth.registration');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'phone_number' => 'required|string|unique:users|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if email or phone number already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email already exists'], 422);
        }

        if (User::where('phone_number', $request->phone_number)->exists()) {
            return response()->json(['message' => 'Phone number already exists'], 422);
        }

        // Hash the password
        $password = Hash::make($request->password);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => $password,
        ]);

        // Generate a verification code
        $verificationCode = $this->generateVerificationCode();

        // Store the verification code in the user's record
        $user->phone_verification_code = $verificationCode;
        $user->save();

        // Send verification code to phone number
        try {
            $this->sendVerificationCode($user->phone_number, $verificationCode);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['message'=>$e->getMessage()]);
            //return redirect()->back()->with('message', 'Failed to send verification code: ' . $e->getMessage())->withInput();
        }
        // return response()->json(['message' => 'User created successfully'], 201);
        return redirect()->route('verification.show', ['phone_number' => $user->phone_number]);
    }

    /**
     * Generate a random verification code.
     */
    private function generateVerificationCode()
    {
        return rand(100000, 999999); // Random 6-digit code
    }

    /**
     * Send the verification code via SMS.
     */
    private function sendVerificationCode($phoneNumber, $verificationCode)
    {
        $sid = env("TWILIO_SID");
        $token = env("TWILIO_TOKEN");
        $twilioNumber = env("TWILIO_FROM");

        $client = new Client($sid, $token);

        $client->messages->create(
            $phoneNumber,
            [
                'from' => $twilioNumber,
                'body' => 'Your verification code is ' . $verificationCode,
            ]
        );
    }

public function showVerificationForm()
{
    return view('auth.verification.form');
}

public function verifyPhoneNumber(Request $request)
{
    $validator = Validator::make($request->all(), [
        'verification_code' => 'required|string|size:6',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Assuming the user is already authenticated
    if(!auth()->check()){
        return Response()->json(['message'=>'not Authenticated']);
    }
    $user = auth()->user();

    if ($user->verification_code === $request->verification_code) {
        // Verification successful, update user record
        $user->phone_verified = true;
        $user->save();

        return redirect()->route('home')->with('success', 'Phone number verified successfully.');
    } else {
        // Verification failed
        return back()->with('error', 'Invalid verification code. Please try again.');
    }
}
}
