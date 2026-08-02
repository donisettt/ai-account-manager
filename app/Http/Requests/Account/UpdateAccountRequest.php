<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::unique('accounts', 'email')->ignore($this->account)],
            'password' => ['nullable', 'string', 'min:6'], // Nullable karena tidak wajib diubah
            'provider' => ['required', 'string', 'max:100'],
            'provider_login' => ['nullable', 'string', 'max:255'],
            'recovery_email' => ['nullable', 'email'],
            'status' => ['required', Rule::in(['Ready', 'In Use', 'Suspended', 'Expired'])],
            'catatan' => ['nullable', 'string'],
            'tools' => ['nullable', 'array'],
            'tools.*' => ['exists:tools,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 6 karakter',
            'provider.required' => 'Provider wajib dipilih',
            'status.required' => 'Status wajib dipilih',
            'recovery_email.email' => 'Format recovery email tidak valid',
            'tools.*.exists' => 'Tool yang dipilih tidak valid',
        ];
    }
}
