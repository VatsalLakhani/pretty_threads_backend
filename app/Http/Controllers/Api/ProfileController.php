<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Return the authenticated user's profile.
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the authenticated user's profile fields.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => ['sometimes','required','string','max:255'],
            'email' => ['sometimes','required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['sometimes','required','string','max:20', Rule::unique('users')->ignore($user->id)],
            'full_address' => ['sometimes','required','string'],
            'city' => ['sometimes','required','string','max:100'],
            'pincode' => ['sometimes','required','string','max:10'],
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required','string'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Permanently delete the authenticated user's account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens for safety
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // Delete user account
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted successfully',
        ], 200);
    }
}
