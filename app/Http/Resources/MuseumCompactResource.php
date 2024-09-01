<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MuseumCompactResource extends JsonResource
{

    public function toArray($request): array
    {

        return [

            'id' => $this->id,
            'coleccion' => 'museum',

            // Seo
            'slug' => $this->getRouteKey(),

            // Datos generales
            'codigo' => $this->codigo,
            'tipo_recurso' => $this->tipoRecurso,
            'nombre' => $this->nombre,

            // Datos generales / localización
            'nombre_municipio' => $this->nombreMunicipio,
            'nombre_localidad' => $this->nombreLocalidad,

            // Datos generales / georeferenciación
            'longitud' => $this->gmLongitud,
            'latitud' => $this->gmLatitud,

            // Datos arte y cultura
            'subtipo_recurso' => $this->subTipoRecurso,
            'nombre_subtipo_recurso' => $this->nombreSubTipoRecurso,

            // Relaciones
            'imagenes' => $this->images->isNotEmpty() ? [new ImageResource($this->images->first())] : []

        ];
    }
}
