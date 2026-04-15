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
        // the user id is passed as a string parameter 'id' or 'user' to the route depending on the resource name
        $userId = $this->route('id') ?? $this->route('user');
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['nullable', 'string'],
        ];

        if ($userId) {
            $rules['email'][] = Rule::unique('users', 'email')->ignore($userId);
            $rules['password'] = ['nullable', 'string', 'min:8'];
        } else {
            $rules['email'][] = Rule::unique('users', 'email');
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $rules;
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
