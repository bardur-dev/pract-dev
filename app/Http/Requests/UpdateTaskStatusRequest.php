<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateTaskStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => [
                'required',
                Rule::in(TaskStatus::values())
            ],
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'Выбран недопустимый статус задачи',
        ];
    }
}
