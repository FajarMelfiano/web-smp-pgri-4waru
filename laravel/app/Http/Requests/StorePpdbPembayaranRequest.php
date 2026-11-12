<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePpdbPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('ppdb')->check();
    }

    protected function prepareForValidation(): void
    {
        if (! $this->hasFile('bukti') && $this->hasFile('bukti_bayar')) {
            $this->files->set('bukti', $this->file('bukti_bayar'));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'metode' => ['required', 'in:transfer,kantor,tunai_tu'],
            'bukti' => ['required_if:metode,transfer', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'catatan_siswa' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'metode.required' => 'Pilih metode pembayaran terlebih dahulu.',
            'bukti.required_if' => 'Unggah bukti transfer (JPG, PNG, WebP, atau PDF) untuk metode transfer.',
            'bukti.mimes' => 'Bukti transfer harus berupa file JPG, PNG, WebP, atau PDF.',
            'bukti.max' => 'Ukuran file bukti transfer maksimal 4 MB.',
        ];
    }
}
