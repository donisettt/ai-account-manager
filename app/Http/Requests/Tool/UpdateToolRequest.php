<?php

namespace App\Http\Requests\Tool;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateToolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', Rule::unique('tools', 'nama')->ignore($this->tool)],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'status_aktif' => ['boolean'],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama tool wajib diisi',
            'nama.unique' => 'Nama tool sudah terdaftar',
            'logo.image' => 'Logo harus berupa gambar',
            'logo.mimes' => 'Logo harus berformat: jpeg, png, jpg, gif, svg',
            'logo.max' => 'Ukuran logo maksimal 2MB',
        ];
    }
}
