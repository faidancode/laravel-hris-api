<?php

namespace App\Http\Requests;

use App\DTOs\PositionDto;
use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Menentukan apakah ini request untuk Update
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            // Jika update, gunakan 'sometimes'. Jika store, wajib 'required'.
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'department_id' => [$isUpdate ? 'sometimes' : 'required', 'string', 'uuid', 'exists:departments,id'],
        ];
    }

    public function toDto(): PositionDto
    {
        return new PositionDto(
            // Menggunakan validated() memastikan hanya data yang lolos aturan yang masuk ke DTO
            name: $this->validated('name'),
            department_id: $this->validated('department_id'),
        );
    }
}