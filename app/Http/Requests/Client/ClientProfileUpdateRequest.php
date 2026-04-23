<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ClientProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('client')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = auth('client')->id();
        $clientType = auth('client')->user()->type;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', "unique:clients,email,{$clientId}"],
            'company_name' => $clientType === 'company' ? ['required', 'string', 'max:255'] : ['nullable'],
            'description' => $clientType === 'company' ? ['nullable', 'string', 'max:1000'] : ['nullable'],
            'phone' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'],
            'timezone' => ['required', 'timezone'],
            'language' => ['required', 'in:fr,en,de'],
            'billing_email' => $clientType === 'company' ? ['nullable', 'email:rfc'] : ['nullable'],
            'same_as_main_email' => ['nullable', 'boolean'],
            'contact_email' => $clientType === 'company' ? ['nullable', 'email:rfc'] : ['nullable'],
            'same_contact_email' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ];
    }
}
