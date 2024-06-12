<?php

namespace App\Http\Controllers;

use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class login extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('login', 'password');

        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
            $user = user::where($loginType, $credentials['login'])->first();
            if ($user && password_verify($credentials['password'], $user->password_hashed)) {

                Auth::login($user);
                if($user->email =='admin@gmail.com'){
                    return redirect()->route('admin.dashboard');
                }else{
                return redirect()->route('showproducts')->with('success', 'Login successful!');
                }
            } else {
                return redirect()->back()->withErrors(['login' => 'Invalid credentials']);
            }

    }
}
