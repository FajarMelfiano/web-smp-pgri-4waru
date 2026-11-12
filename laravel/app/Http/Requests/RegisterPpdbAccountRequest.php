<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterPpdbAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        $email = strtolower(trim((string) $this->input('email', '')));
        $password = (string) $this->input('password', '');
        $passwordConfirmation = (string) $this->input('password_confirmation', '');

        if ($email !== '' && $password !== '' && $password === $passwordConfirmation && ! $validator->errors()->hasAny(['email', 'password'])) {
            $this->session()->put('ppdb_reg_draft', [
                'email' => $email,
                'password' => $password,
            ]);
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    protected function prepareForValidation(): void
    {
        $rawWa = (string) preg_replace('/[^0-9]/', '', (string) $this->input('whatsapp', ''));
        if ($rawWa !== '') {
            if (str_starts_with($rawWa, '0')) {
                $rawWa = '62'.substr($rawWa, 1);
            } elseif (! str_starts_with($rawWa, '62')) {
                $rawWa = '62'.$rawWa;
            }
        }

        $draft = $this->session()->get('ppdb_reg_draft', []);
        $email = strtolower(trim((string) $this->input('email', $draft['email'] ?? '')));
        $password = (string) $this->input('password', '');
        $passwordConfirmation = (string) $this->input('password_confirmation', '');

        if ($password === '' && ! empty($draft['password'])) {
            $password = (string) $draft['password'];
            $passwordConfirmation = (string) $draft['password'];
        }

        $this->merge([
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'nik' => (string) preg_replace('/[^0-9]/', '', (string) $this->input('nik', '')),
            'whatsapp' => $rawWa,
            'nama_lengkap' => trim((string) $this->input('nama_lengkap', '')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:150', 'unique:ppdb_accounts,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'confirmed',
            ],
            'nama_lengkap' => ['required', 'string', 'max:120'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'whatsapp' => ['required', 'string', 'regex:/^62[0-9]{8,13}$/'],
            'nik' => ['required', 'string', 'digits:16', 'unique:ppdb_accounts,nik'],
            'website_url' => ['nullable', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Masukkan alamat email calon siswa.',
            'email.email' => 'Format alamat email belum valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk menggunakan akun Anda.',
            'password.required' => 'Buat kata sandi terlebih dahulu.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 16 karakter.',
            'password.regex' => 'Kata sandi harus memuat huruf besar, huruf kecil, dan minimal satu angka.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'nama_lengkap.required' => 'Masukkan nama lengkap calon siswa.',
            'tanggal_lahir.required' => 'Pilih tanggal lahir calon siswa.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'whatsapp.required' => 'Masukkan nomor WhatsApp orang tua atau wali.',
            'whatsapp.regex' => 'Nomor WhatsApp harus valid (8 sampai 13 angka setelah +62).',
            'nik.required' => 'Masukkan 16 digit NIK calon siswa.',
            'nik.digits' => 'NIK calon siswa wajib tepat 16 angka.',
            'nik.unique' => 'NIK ini sudah terdaftar. Silakan masuk melalui halaman login.',
            'website_url.max' => 'Permintaan tidak valid.',
        ];
    }
}
