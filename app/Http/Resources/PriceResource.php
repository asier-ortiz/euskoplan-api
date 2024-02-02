<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'nombre' => $this->nombre,
            'capacidad' => $this->capacidad,
            'precio_minimo' => $this->precioMinimo,
            'precio_maximo' => $this->precioMaximo
        ];
    }
}
