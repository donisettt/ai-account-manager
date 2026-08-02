<?php

namespace App\Http\Requests\UsageLog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsageLogRequest extends FormRequest
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
            'tanggal' => ['required', 'date'],
            'limit_used' => ['required', 'numeric', 'min:0'],
            'limit_total' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Ready', 'Warning', 'Limit', 'Maintenance', 'Sedang Dipakai'])],
            'catatan' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'Account wajib dipilih',
            'account_id.exists' => 'Account tidak valid',
            'tool_id.required' => 'Tool wajib dipilih',
            'tool_id.exists' => 'Tool tidak valid',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'limit_used.required' => 'Limit yang digunakan wajib diisi',
            'limit_used.numeric' => 'Limit yang digunakan harus berupa angka',
            'limit_total.required' => 'Total limit wajib diisi',
            'limit_total.numeric' => 'Total limit harus berupa angka',
            'status.required' => 'Status wajib dipilih',
        ];
    }
}
