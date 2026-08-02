<?php

namespace App\Http\Requests\ActivityLog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'tool_id' => ['required', 'exists:tools,id'],
            'waktu' => ['required', 'date'],
            'aktivitas' => ['required', Rule::in(['Dipakai', 'Limit', 'Reset', 'Login', 'Logout', 'Error', 'Maintenance'])],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'Account wajib dipilih',
            'account_id.exists' => 'Account tidak valid',
            'tool_id.required' => 'Tool wajib dipilih',
            'tool_id.exists' => 'Tool tidak valid',
            'waktu.required' => 'Waktu wajib diisi',
            'waktu.date' => 'Format waktu tidak valid',
            'aktivitas.required' => 'Aktivitas wajib dipilih',
        ];
    }
}
