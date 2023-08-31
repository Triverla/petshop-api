<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function __construct(User $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @return array<string, array<string>|string>
     */
    public function toArray(Request $request): array
    {
        if (count($this->additional)) {
            return array_merge($this->default(), $this->additional);
        }

        return $this->default();
    }

    /**
     * @return array<string, array<string>|string>
     */
    private function default(): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'email_verified_at' => $this->resource->email_verified_at,
            'avatar' => $this->resource->avatar,
            'is_marketing' => $this->resource->is_marketing,
            'address' => $this->resource->address,
            'phone_number' => $this->resource->phone_number,
            'updated_at' => $this->resource->updated_at,
            'created_at' => $this->resource->created_at,
            'last_login' => $this->resource->last_login,
        ];
    }
}
