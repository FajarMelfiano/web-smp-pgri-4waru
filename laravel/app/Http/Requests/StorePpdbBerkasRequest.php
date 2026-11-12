<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePpdbBerkasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('ppdb')->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $jenis = (string) $this->input('jenis', '');
        $mimes = $jenis === 'foto' ? 'mimes:jpg,jpeg,png,webp' : 'mimes:jpg,jpeg,png,webp,pdf';

        return [
            'jenis' => ['required', 'in:kk,akta,skl,foto,prestasi'],
            'file' => ['required', 'file', $mimes, 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis berkas tidak valid.',
            'jenis.in' => 'Jenis berkas tidak dikenali.',
            'file.required' => 'Pilih file berkas yang akan diunggah.',
            'file.mimes' => 'Format file tidak didukung. Gunakan JPG, PNG, WebP (atau PDF untuk dokumen).',
            'file.max' => 'Ukuran berkas maksimal 4 MB.',
        ];
    }
}
