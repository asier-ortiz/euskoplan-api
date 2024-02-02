<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocalityResource extends JsonResource
{

    public function toArray($request): array
    {

        // Pivot
        if (isset($this->pivot->id)) $resource['id'] = $this->pivot->id;
        if (isset($this->pivot->indice)) $resource['indice'] = $this->pivot->indice;
        if (isset($this->pivot->indicaciones)) $resource['indicaciones'] = $this->pivot->indicaciones;

        $object = [

            'id' => $this->id,
            'coleccion' => 'locality',

            // Datos generales
            'codigo' => $this->codigo,
            'tipo_recurso' => $this->tipoRecurso,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'url_ficha_portal' => $this->urlFichaPortal,

            // Datos generales / localización
            'codigo_provincia' => $this->codigoProvincia,
            'codigo_municipio' => $this->codigoMunicipio,
            'codigo_localidad' => $this->codigoLocalidad,
            'nombre_provincia' => $this->nombreProvincia,
            'nombre_municipio' => $this->nombreMunicipio,
            'nombre_localidad' => $this->nombreLocalidad,

            // Datos generales / georeferenciación
            'longitud' => $this->gmLongitud,
            'latitud' => $this->gmLatitud,

            // Datos localidad
            'numero_habitantes' => $this->numHabitantes,
            'superficie' => $this->superficie,

            // Relaciones
            'imagenes' => ImageResource::collection($this->images)
        ];

        $resource['recurso'] = $object;

        return isset($this->pivot->id) ? $resource : $object;
    }
}
