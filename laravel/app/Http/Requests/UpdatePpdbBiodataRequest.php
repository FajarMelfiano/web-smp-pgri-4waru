<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePpdbBiodataRequest extends FormRequest
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
        return [
            'nisn' => ['nullable', 'digits_between:8,12'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan,L,P'],
            'tempat_lahir' => ['required', 'string', 'max:80'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'alamat_lengkap' => ['required', 'string', 'max:500'],
            'kelurahan_desa' => ['required', 'string', 'max:80'],
            'kecamatan' => ['required', 'string', 'max:80'],
            'kabupaten_kota' => ['required', 'string', 'max:80'],
            'asal_sd' => ['required', 'string', 'max:140'],
            'npsn_sd' => ['nullable', 'digits_between:8,10'],
            'tahun_lulus_sd' => ['required', 'digits:4'],
            'anak_ke' => ['nullable', 'integer', 'min:1', 'max:20'],
            'jumlah_saudara' => ['nullable', 'integer', 'min:0', 'max:20'],
            'nama_ayah' => ['required', 'string', 'max:120'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:80'],
            'no_hp_ayah' => ['nullable', 'string', 'max:25'],
            'nama_ibu' => ['required', 'string', 'max:120'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:80'],
            'no_hp_ibu' => ['nullable', 'string', 'max:25'],
            'nama_wali' => ['nullable', 'string', 'max:120'],
            'hubungan_wali' => ['nullable', 'string', 'max:60'],
            'penghasilan_ortu' => ['nullable', 'string', 'max:60'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nisn.digits_between' => 'NISN harus berisi 8 sampai 12 angka.',
            'jenis_kelamin.required' => 'Pilih jenis kelamin calon siswa.',
            'tempat_lahir.required' => 'Isi tempat lahir sesuai akta kelahiran.',
            'agama.required' => 'Pilih agama calon siswa.',
            'alamat_lengkap.required' => 'Isi alamat domisili lengkap.',
            'kelurahan_desa.required' => 'Isi nama kelurahan atau desa.',
            'kecamatan.required' => 'Isi nama kecamatan.',
            'kabupaten_kota.required' => 'Isi nama kabupaten atau kota.',
            'asal_sd.required' => 'Isi nama SD atau MI asal.',
            'tahun_lulus_sd.required' => 'Isi tahun lulus SD/MI (4 digit).',
            'tahun_lulus_sd.digits' => 'Tahun lulus SD harus 4 digit angka.',
            'anak_ke.required' => 'Isi urutan anak ke berapa.',
            'jumlah_saudara.required' => 'Isi jumlah saudara kandung.',
            'nama_ayah.required' => 'Isi nama lengkap ayah.',
            'pekerjaan_ayah.required' => 'Isi pekerjaan ayah.',
            'nama_ibu.required' => 'Isi nama lengkap ibu.',
            'pekerjaan_ibu.required' => 'Isi pekerjaan ibu.',
            'penghasilan_ortu.required' => 'Pilih rentang penghasilan orang tua/wali.',
        ];
    }
}
