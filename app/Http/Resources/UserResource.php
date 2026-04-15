<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // app/Http/Resources/AuthResource.php

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'employeeId' => $this->employee_id,
            'isActive' => (bool) $this->is_active,
            'emailVerifiedAt' => $this->email_verified_at,
            'createdAt' => $this->created_at,

            // Flatten roles menjadi array string sederhana
            'roles' => $this->whenLoaded('roles', function () {
                return $this->getRoleNames(); // Mengembalikan ['superadmin']
            }),

            // Flatten permissions menjadi array string sederhana
            'permissions' => $this->whenLoaded('roles', function () {
                return $this->getAllPermissions()->pluck('name'); // Mengembalikan ['user.view', 'user.create', ...]
            }),
        ];
    }
}
