<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'   => 'required|string|max:150',
            'message' => 'required|string|max:1000',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Judul catatan wajib diisi.',
            'message.required' => 'Detail catatan wajib diisi.',
            'image.image'      => 'File yang diupload harus berupa gambar.',
            'image.max'        => 'Ukuran foto maksimal adalah 10MB.',
        ];
    }
}
