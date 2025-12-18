<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;  // Base controller
use App\Models\User;                   // Model User untuk interaksi database
use Illuminate\Support\Facades\Auth;   // Facade untuk authentication
use Illuminate\Support\Facades\Hash;   // Facade untuk hashing password
use Illuminate\Support\Str;             // Helper untuk string manipulation
use Laravel\Socialite\Facades\Socialite; // ⭐ Package Socialite untuk OAuth
use Exception;                          // Class untuk handle error

class GoogleController extends Controller
{
    /**
     * Redirect user ke halaman OAuth Google.
     *
     * Method ini dipanggil ketika user klik tombol "Login dengan Google".
     * Socialite akan membangun URL lengkap dengan semua parameter OAuth.
     *
     * Route: GET /auth/google
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['email', 'profile'])
            ->redirect();
    }

    /**
     * Handle callback dari Google setelah user memberikan izin.
     *
     * Method ini dipanggil oleh Google setelah user klik "Allow".
     * Google akan mengirimkan authorization_code ke URL ini.
     *
     * Route: GET /auth/google/callback
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback()
    {
   
        if (request()->has('error')) {
            $error = request('error');
            // ↑ request('error') mengambil nilai parameter ?error=xxx dari URL

            if ($error === 'access_denied') {
                return redirect()
                    ->route('login')
                    ->with('info', 'Login dengan Google dibatalkan.');
                // ↑ with('info', '...') = Flash message untuk ditampilkan sekali
            }

            return redirect()
                ->route('login')
                ->with('error', 'Terjadi kesalahan: ' . $error);
        }

        try {

            $googleUser = Socialite::driver('google')->user();
    
            $user = $this->findOrCreateUser($googleUser);

            Auth::login($user, remember: true);
           
            session()->regenerate();
        
            return redirect()
                ->intended(route('home'))
                // ↑ intended() = Redirect ke halaman yang coba diakses sebelumnya
                // Jika tidak ada, redirect ke 'home'
                ->with('success', 'Berhasil login dengan Google!');
                // ↑ Flash message sukses

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
         
            return redirect()
                ->route('login')
                ->with('error', 'Session telah berakhir. Silakan coba lagi.');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
     
            logger()->error('Google API Error: ' . $e->getMessage());
            // ↑ Log error untuk debugging (lihat di storage/logs/laravel.log)

            return redirect()
                ->route('login')
                ->with('error', 'Terjadi kesalahan saat menghubungi Google. Coba lagi.');

        } catch (Exception $e) {
            // ================================================
            // ERROR: LAINNYA
            // ================================================

            logger()->error('OAuth Error: ' . $e->getMessage());

            return redirect()
                ->route('login')
                ->with('error', 'Gagal login. Silakan coba lagi.');
        }
    }

    /**
     * Cari user berdasarkan Google ID atau email, atau buat user baru.
     *
     * Method ini menangani 3 skenario:
     * 1. User sudah pernah login dengan Google (ada google_id)
     * 2. User sudah register manual dengan email yang sama
     * 3. User benar-benar baru
     *
     * @param \Laravel\Socialite\Contracts\User $googleUser Data user dari Google
     * @return \App\Models\User User dari database
     */
    protected function findOrCreateUser($googleUser): User
    {

        $user = User::where('google_id', $googleUser->getId())->first();
        // ↑ Cari di tabel users WHERE google_id = '...'

        if ($user) {
            // User ditemukan! Cek apakah avatar berubah
            if ($user->avatar !== $googleUser->getAvatar()) {
                $user->update(['avatar' => $googleUser->getAvatar()]);
                // ↑ Update avatar jika user ganti foto profil di Google
            }
            return $user;
            // ↑ Langsung return user yang sudah ada
        }

        $user = User::where('email', $googleUser->getEmail())->first();
        // ↑ Cari di tabel users WHERE email = '...'

        if ($user) {
            // User dengan email ini sudah ada!
            // Link akun Google ke user yang sudah ada

            $user->update([
                'google_id' => $googleUser->getId(),
                // ↑ Simpan Google ID untuk login berikutnya

                'avatar' => $googleUser->getAvatar() ?? $user->avatar,
                // ↑ Update avatar (gunakan yang lama jika Google tidak ada)

                'email_verified_at' => $user->email_verified_at ?? now(),
                // ↑ Tandai email verified (Google sudah verifikasi)
            ]);

            return $user;
        }

        return User::create([
            'name' => $googleUser->getName(),
            // ↑ Nama dari profil Google

            'email' => $googleUser->getEmail(),
            // ↑ Email dari Google (verified)

            'google_id' => $googleUser->getId(),
            // ↑ Google ID untuk login berikutnya

            'avatar' => $googleUser->getAvatar(),
            // ↑ URL foto profil dari Google

            'email_verified_at' => now(),
            // ↑ Langsung verified karena Google sudah verifikasi

            'password' => Hash::make(Str::random(24)),
        
            'role' => 'customer',
            // ↑ Role default untuk user baru
        ]);
    }
}