<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['sometimes', 'required', 'exists:projects,id'],
            'staff_id' => ['nullable', 'exists:staff,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', 'required', 'string', 'in:low,medium,high,urgent'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,in_progress,completed,cancelled'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
