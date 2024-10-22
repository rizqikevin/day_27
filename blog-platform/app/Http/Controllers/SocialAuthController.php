<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    // Redirect the user to the Google authentication page.
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle the callback from Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if the user already exists in the database
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // If the user exists, log them in
                Auth::login($existingUser);
            } else {
                // If the user does not exist, create a new one
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'provider_name' => 'google',
                    'provider_id' => $googleUser->getId(),
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Something went wrong, please try again.');
        }
    }
}
