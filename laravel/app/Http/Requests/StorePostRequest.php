<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $postId = $this->route('post')?->id ?? $this->input('post_id');

        return [
            'judul' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('posts', 'slug')->ignore($postId)],
            'ringkasan' => ['nullable', 'string', 'max:300'],
            'isi' => ['required', 'string'],
            'status' => ['required', Rule::in(['draf', 'terbit'])],
            'cover' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'cover_preset' => ['nullable', 'string', 'max:255'],
        ];
    }
}
