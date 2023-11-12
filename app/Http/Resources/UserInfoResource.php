<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'first_name' => $this['first_name'],
            'last_name' => $this['last_name'],
            'email' => $this['email'],
            'phone_number' => $this['phone_number'],
            'congregation_id' => $this['congregation_id'],
            'congregation_name' => $this['congregation_name'],
            'congregation_code' => $this['congregation_code'],
            'roles' => $this['roles']->pluck('name'),
            'permissions' => $this['permissions'],
        ];
    }
}
