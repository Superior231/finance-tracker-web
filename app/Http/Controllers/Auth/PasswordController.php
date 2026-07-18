<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        try {
            $updated = $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            if ($updated) {
                return redirect()->route('profile.index')->with('success', 'Password updated successfully!');
            } else {
                return redirect()->route('profile.index')->with('error', 'Failed to update password!');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('profile.index')->with('error', 'Oops... Something went wrong!');
        }
    }
}
