<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; // 👈 ថែមជួរមួយនេះចូល

class ProfileController extends Controller
{
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

        // 1. Fill ទិន្នន័យអក្សរទាំងអស់ (name, email, gender...) ចូលទៅក្នុង Model មុនគេ
        $user->fill($request->validated());

        // 2. បន្ទាប់មកទើបពិនិត្យ និងចាត់ចែងការ Upload រូបភាពតាមក្រោយ
        if ($request->hasFile('image')) {
            // លុបរូបភាពចាស់ចេញពី Storage (បើមាន)
            if ($user->getOriginal('image') && Storage::disk('public')->exists($user->getOriginal('image'))) {
                Storage::disk('public')->delete($user->getOriginal('image'));
            }

            // ប្រើ store() ដើម្បីផ្លាស់ទីរូបភាពទៅ storage/app/public/profile-images
            $path = $request->file('image')->store('profile-images', 'public');

            // កំណត់ Path ថ្មីចូលទៅក្នុង Model (វានឹងជាន់លើតម្លៃចាស់ដោយសុវត្ថិភាព)
            $user->image = $path;
        }

        // 3. រក្សាទុកទិន្នន័យទាំងអស់ចូល Database ព្រមគ្នាតែម្តង
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    // app/Http/Controllers/ProfileController.php


}
