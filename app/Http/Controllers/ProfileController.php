<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    public function show()
    {
        return view('profile', [
            'user' => Auth::user(),
            'editMode' => false
        ]);
    }

    // Show editable profile form
    public function edit()
    {
        return view('profile', [
            'user' => Auth::user(),
            'editMode' => true
        ]);
    }


    public function update(Request $request)
    {
        $user = Auth::user();
        $input = $request->all();

        // Validate input
        $request->validate([
            'nickname' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = $avatar->storeAs('avatars', $avatarName, 'public');

            // Delete old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $input['avatar'] = $avatarPath;
        }

        // Handle password separately (hashing)
        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->password);
        } else {
            unset($input['password']);
        }

        // Update the user
        $updateStatus = $user->update($input);

        if ($updateStatus) {
            Auth::setUser($user->fresh());
            return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong. Profile not updated.');
        }
    }

    // Delete account
    public function destroy()
    {
        $user = Auth::user();

        // Delete avatar file if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}
