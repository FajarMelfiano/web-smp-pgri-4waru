<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePendaftarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_siswa' => $this->input('nama_siswa', $this->input('siswa')),
            'asal_sd' => $this->input('asal_sd', $this->input('asal')),
            'nama_wali' => $this->input('nama_wali', $this->input('wali')),
            'whatsapp' => preg_replace('/[\s\-()+]/', '', (string) $this->input('whatsapp', $this->input('wa'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_siswa' => ['required', 'string', 'max:120'],
            'asal_sd' => ['required', 'string', 'max:120'],
            'nama_wali' => ['required', 'string', 'max:120'],
            'whatsapp' => ['required', 'string', 'regex:/^[0-9]{9,15}$/'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'website_url' => ['nullable', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_siswa.required' => 'Tulis nama calon siswa.',
            'asal_sd.required' => 'Tulis nama SD asal.',
            'nama_wali.required' => 'Tulis nama orang tua atau wali.',
            'whatsapp.required' => 'Tulis nomor WhatsApp, 9 sampai 15 angka.',
            'whatsapp.regex' => 'Tulis nomor WhatsApp, 9 sampai 15 angka.',
            'website_url.max' => 'Permintaan tidak valid.',
        ];
    }
}
