<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'staff_id' => ['nullable', 'exists:staff,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'status' => ['required', 'string', 'in:pending,in_progress,completed,cancelled'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
