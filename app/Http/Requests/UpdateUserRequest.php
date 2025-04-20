<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'role' => ['required', 'string', Rule::in(Role::values())]
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Этот email уже занят',
            'role.in' => 'Недопустимая роль пользователя'
        ];
    }
}
