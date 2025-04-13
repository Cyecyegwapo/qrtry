<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest; // We'll create this next
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     *
     * @param Request $request
     * @return View
     */
    public function edit(Request $request): View
    {
        // Return the profile edit view, passing the authenticated user
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param ProfileUpdateRequest $request // Use the form request for validation
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Fill the user model with validated data
        $request->user()->fill($request->validated());

        // If the email address was changed, reset the email verification status
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Save the changes to the user model
        $request->user()->save();

        // Redirect back to the profile edit page with a success status message
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validate that the provided password is correct
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Log the user out first
        Auth::logout();

        // Delete the user account
        $user->delete();

        // Invalidate the session and regenerate the token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the application's home page
        return Redirect::to('/');
    }
}
