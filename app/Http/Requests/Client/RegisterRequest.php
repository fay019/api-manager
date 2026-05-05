<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('type', 'person');

        return [
            'type' => ['required', 'in:person,company'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'company_name' => $type === 'company' ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email'],
            'contact_email' => $type === 'company' ? ['nullable', 'email', 'max:255'] : ['nullable'],
            'billing_email' => $type === 'company' ? ['nullable', 'email', 'max:255'] : ['nullable'],
            'same_as_main_email' => ['nullable', 'boolean'],
            'same_contact_email' => ['nullable', 'boolean'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ];
    }
}
