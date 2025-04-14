<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreOrCreateRequests extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Название задачи обязательно',
            'name.min' => 'Минимальная длина названия - 3 символа'
        ];
    }
}
