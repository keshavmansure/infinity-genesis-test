<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            $this->merge(new LinkResource($this)),
            'analytics_count' => $this->analytics_count,
            'user' => new UserResource($this->user),
        ];
    }
}
