<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'short_link' => $this->shortLink(),
            'original_url' => $this->original_url,
            'is_active' => (bool)$this->is_active,
            'expire_at' => $this->expire_at,
            'created_at' => $this->created_at,
        ];
    }
    public function shortLink()
    {
        return url('/links/' . $this->short_link);
    }
}
