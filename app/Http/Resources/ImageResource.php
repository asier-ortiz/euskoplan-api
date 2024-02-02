<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'fuente' => $this->src,
            'titulo' => $this->titulo
        ];
    }
}
