<?php

namespace App\Http\Requests;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrCreateRequests extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(TaskStatus::values()),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Название задачи обязательно',
            'name.min' => 'Минимальная длина названия - 3 символа',
            'name.max' => 'Максимальная длина названия - 255 символов',
            'status.in' => 'Выбран недопустимый статус задачи',
        ];
    }
}
