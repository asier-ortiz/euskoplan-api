<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NaturalCompactResource extends JsonResource
{

    public function toArray($request): array
    {

        return [

            'id' => $this->id,
            'coleccion' => 'natural',

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

            // Datos espacios naturales
            'subtipo_recurso_espacio_natural' => $this->subTipoRecursoEspacioNatural,
            'nombre_subtipo_recurso_espacio_natural' => $this->nombreSubTipoRecursoEspacioNatural,

            // Datos playas, pantanos y ríos
            'subtipo_recurso_playas_pantanos_rios' => $this->subTipoRecursoPlayasPantanosRios,
            'nombre_subtipo_recurso_playas_pantanos_rios' => $this->nombreSubTipoRecursoPlayasPantanosRios,

            // Relaciones
            'imagenes' => $this->images->isNotEmpty() ? [new ImageResource($this->images->first())] : []

        ];
    }
}
