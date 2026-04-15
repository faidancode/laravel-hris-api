<?php

namespace App\Http\Requests;

use App\DTOs\AuthDto;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDto(): AuthDto
    {
        return new AuthDto(
            email: $this->validated('email'),
            password: $this->validated('password'),
        );
    }
}
