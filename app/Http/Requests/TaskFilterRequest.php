<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TaskStatus;
class TaskFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:' . implode(',', TaskStatus::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Выбранный статус недействителен.',
        ];
    }
}
