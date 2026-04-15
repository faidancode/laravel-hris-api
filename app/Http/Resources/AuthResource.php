<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
            'user' => new UserResource($this['user']), // Memanggil UserResource agar data user konsisten
            'accessToken' => $this['access_token'],
            'refreshToken' => $this['refresh_token'], // Hanya dikirim saat login/refresh
            'expiresIn' => $this['expires_in'],
            'roles' => $this['roles'],
            'permissions' => $this['permissions'],
        ];
    }
}
