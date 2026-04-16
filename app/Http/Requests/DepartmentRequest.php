<?php

namespace App\Http\Requests;

use App\DTOs\DepartmentDto;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
        ];
    }

    public function toDto(): DepartmentDto
    {
        return new DepartmentDto(
            // Menggunakan validated() memastikan hanya data yang lolos aturan yang masuk ke DTO
            name: $this->validated('name'),
        );
    }
}