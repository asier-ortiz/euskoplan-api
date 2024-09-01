<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantCompactResource extends JsonResource
{

    public function toArray($request): array
    {

        return [

            'id' => $this->id,
            'coleccion' => 'restaurant',

            // Seo
            'slug' => $this->getRouteKey(),

            // Datos generales
            'codigo' => $this->codigo,
            'tipo_recurso' => $this->tipoRecurso,
            'nombre' => $this->nombre,

            // Datos generales / localización
            'nombre_provincia' => $this->nombreProvincia,
            'nombre_municipio' => $this->nombreMunicipio,

            // Datos generales / georeferenciación
            'longitud' => $this->gmLongitud,
            'latitud' => $this->gmLatitud,

            // Datos restauración
            'subtipo_recurso' => $this->subtipoRecurso,
            'nombre_subtipo_recurso' => $this->nombreSubtipoRecurso,

            // Relaciones
            'imagenes' => $this->images->isNotEmpty() ? [new ImageResource($this->images->first())] : []

        ];
    }
}
