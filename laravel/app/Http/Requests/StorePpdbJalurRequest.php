<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePpdbJalurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $kodeInput = (string) $this->input('kode', '');
        $namaInput = (string) $this->input('nama', '');

        $this->merge([
            'kode' => Str::slug($kodeInput !== '' ? $kodeInput : $namaInput),
            'biaya_daftar' => (int) preg_replace('/[^0-9]/', '', (string) $this->input('biaya_daftar', '0')),
            'kuota' => $this->filled('kuota') ? (int) $this->input('kuota') : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $jalurId = $this->route('jalur')?->id;

        return [
            'nama' => ['required', 'string', 'max:100'],
            'kode' => ['required', 'string', 'max:40', 'alpha_dash', Rule::unique('ppdb_jalur', 'kode')->ignore($jalurId)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'persyaratan' => ['nullable', 'string', 'max:1000'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'biaya_daftar' => ['required', 'integer', 'min:0', 'max:50000000'],
            'kuota' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'dibuka' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
