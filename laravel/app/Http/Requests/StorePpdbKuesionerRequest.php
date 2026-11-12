<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePpdbKuesionerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('ppdb')->check();
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('siap_tata_tertib') && $this->has('pernyataan_setuju')) {
            $this->merge(['siap_tata_tertib' => $this->input('pernyataan_setuju')]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sumber_info' => ['required', 'string', 'max:120'],
            'alasan_memilih' => ['required', 'string', 'max:500'],
            'minat_ekstrakurikuler' => ['nullable', 'array', 'max:5'],
            'minat_ekstrakurikuler.*' => ['string', 'max:60'],
            'siap_tata_tertib' => ['accepted'],
            'saran' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sumber_info.required' => 'Pilih sumber informasi Anda mengenai SMP PGRI 4 Waru.',
            'alasan_memilih.required' => 'Tulis alasan memilih SMP PGRI 4 Waru.',
            'siap_tata_tertib.accepted' => 'Centang pernyataan kesiapan menaati tata tertib sekolah.',
        ];
    }
}
