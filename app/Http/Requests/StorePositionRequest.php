<?php

namespace App\Http\Requests;

use App\DTOs\PositionDto;
use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'string', 'uuid', 'exists:departments,id'],
        ];
    }

    public function toDto(): PositionDto
    {
        return new PositionDto(
            name: $this->validated('name'),
            department_id: $this->validated('department_id'),
        );
    }
}