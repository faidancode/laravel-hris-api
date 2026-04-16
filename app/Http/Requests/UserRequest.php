<?php

namespace App\Http\Requests;

use App\DTOs\UserDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        // Ambil ID dari rute untuk pengecualian unique
        $id = $this->route('id') ?? $this->route('user');

        return [
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'email' => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['nullable', 'string'],
        ];
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            name: $this->validated('name') ?? $this->input('name', ''), // fallback just in case
            email: $this->validated('email'),
            password: $this->validated('password'),
            role: $this->validated('role'),
        );
    }
}
