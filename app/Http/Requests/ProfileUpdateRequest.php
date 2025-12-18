<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            // Avatar: opsional
            // Harus file gambar (mime: jpg, png, webp)
            // Max ukuran 2MB (2048 KB)
            // Dimensi minimal 100x100px agar tidak pecah/blur
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
        ];
    }

    /**
     * Custom error messages (Bahasa Indonesia).
     * Laravel menyediakan default message (b.inggris), kita override agar lebih user friendly.
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format 08xx atau +628xx.',
            'avatar.max' => 'Ukuran foto maksimal 2MB.',
            'avatar.dimensions' => 'Dimensi foto harus antara 100x100 hingga 2000x2000 pixel.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
        ];
    }

    /**
     * Custom attribute names for error messages.
     * Mengubah ":attribute is required" menjadi "nama wajib diisi".
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'alamat email',
            'phone' => 'nomor telepon',
            'address' => 'alamat domisili',
            'avatar' => 'foto profil',
        ];
    }
}