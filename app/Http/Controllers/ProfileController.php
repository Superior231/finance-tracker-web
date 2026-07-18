<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('pages.profile.index', [
            'title' => 'Profile - Finance Tracker',
            'active' => 'profile',
            'navTitle' => 'Profile',
            'user' => $user,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        if (Auth::id() !== (int) $user->id) {
            return redirect()->route('profile.index')->with('error', 'Oops... Something went wrong!');
        }

        $user->fill($request->validated());

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            $oldAvatar = $user->getOriginal('avatar');
            if ($oldAvatar) {
                Storage::disk('public')->delete('avatars/' . $oldAvatar);
            }
    
            // Upload and update avatar
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('avatars', $fileName, 'public');
            $user->avatar = $fileName;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user) {
            return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->route('profile.index')->with('error', 'Profile failed to update!');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if (Auth::id() !== (int) $user->id) {
            return redirect()->route('profile.index')->with('error', 'Oops... Something went wrong!');
        }

        if (!empty($user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Account deleted successfully!');
    }

    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (Auth::id() !== (int) $user->id) {
            return redirect()->route('profile.index')->with('error', 'Oops... Something went wrong!');
        }

        if (!empty($user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
            $user->avatar = null;
        }

        $user->save();
        
        if ($user) {
            return redirect()->route('profile.index')->with('success', 'Avatar deleted successfully!');
        } else {
            return redirect()->route('profile.index')->with('success', 'Avatar deleted failed!');
        }
    }
}
