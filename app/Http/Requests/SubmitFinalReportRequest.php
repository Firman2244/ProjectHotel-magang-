<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SubmitFinalReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'staff';
    }

    public function rules(): array
    {
        return [
            'save_action' => ['required', 'string', 'in:draft,final'],
            'items.*.status' => ['nullable', 'string', 'in:completed,pending'],
            'items.*.obstacle_note' => ['nullable', 'string', 'max:255'],
            'items.*.before_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'items.*.after_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
            'new_items' => ['nullable', 'array', 'max:20'],
            'new_items.*.custom_task_name' => ['nullable', 'string', 'max:255'],
            'new_items.*.before_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'new_items.*.after_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'new_items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'image' => 'File harus berupa gambar.',
            'mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'max' => 'Ukuran foto maksimal adalah 10MB.',
            'new_items.max' => 'Maksimal tugas tambahan yang dapat diinput adalah 20.',
        ];
    }
}
