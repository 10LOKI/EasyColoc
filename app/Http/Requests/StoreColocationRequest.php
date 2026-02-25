<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreColocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The colocation name is required.',
            'name.max' => 'The colocation name cannot exceed 255 characters.',
        ];
    }
}