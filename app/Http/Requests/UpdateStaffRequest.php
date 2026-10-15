<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff') ?? $this->route('id');
        if (is_object($staffId)) {
            $staffId = $staffId->id;
        }

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('staff', 'email')->ignore($staffId),
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:30'],
            'department' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'is_verified' => ['sometimes', 'boolean'],
        ];
    }
}
