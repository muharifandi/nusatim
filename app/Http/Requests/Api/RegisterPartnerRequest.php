<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:partners,email'],
            'password' => ['required', 'confirmed', Password::default()],
            'profile_photo' => ['required', 'image', 'max:4096'],
            'ktp' => ['required', 'image', 'max:4096'],
            'npwp' => ['nullable', 'image', 'max:4096'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_holder' => ['required', 'string', 'max:255'],
            'agreement_accepted' => ['required', 'accepted'],
        ];
    }
}
