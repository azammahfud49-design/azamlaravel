<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MahasiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',

            'nim' => 'required|string|max:20',

            'jurusan' => 'required|string|max:255',

            'fakultas' => 'required|string|max:255',

            'email' => 'required|email|max:255',

            'nomor_hp' => 'required|string|max:20',

            'alamat' => 'nullable|string',

            'tanggal_lahir' => 'nullable|date',

            'jenis_kelamin' => 'required|in:L,P',

            'foto' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'nim.required' => 'NIM wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
        ];
    }
}