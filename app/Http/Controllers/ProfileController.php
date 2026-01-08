<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

   public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();

    // Update data text dulu
    $user->fill($request->validated());

    // Upload avatar (terpisah)
    if ($request->hasFile('avatar')) {
        $user->avatar = $this->uploadAvatar($request, $user);
    }

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    return Redirect::route('profile.edit')
        ->with('success', 'Profil berhasil diperbarui!');
}

    protected function uploadAvatar(ProfileUpdateRequest $request, $user): string
    {
        // Hapus avatar lama (Garbage Collection)
        // Cek 1: Apakah user punya avatar sebelumnya?
        // Cek 2: Apakah file fisiknya benar-benar ada di storage 'public'?
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Generate nama file unik untuk mencegah bentrok nama.
        // Format: avatar-{user_id}-{timestamp}.{ext}
        $filename = 'avatar-' . $user->id . '-' . time() . '.' . $request->file('avatar')->extension();

        // Simpan file ke folder: storage/app/public/avatars
        // return path relatif: "avatars/namafile.jpg"
        $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');

        return $path;
    }

    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
