<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        if (request()->has('error') || !request()->has('code')) {
            return redirect()->route('login')
                ->with('error', 'Authentication with Google was cancelled.');
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->email)->first();

            if(!$user)
            {
                $user = User::create(
                    ['name' => $googleUser->name,
                     'email' => $googleUser->email,
                     'password' => Hash::make(rand(100000,999999)),
                     'avatar_google' => $googleUser->avatar,
                    ]
                );
            }
    
            Auth::login($user);
            return redirect('/');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Authentication with Google failed. Please try again.');
        }
    }
}
