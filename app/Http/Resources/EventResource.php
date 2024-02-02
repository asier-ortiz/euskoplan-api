<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{

    public function toArray($request): array
    {
        $resource = [];

        // Pivot
        if (isset($this->pivot->id)) $resource['id'] = $this->pivot->id;
        if (isset($this->pivot->indice)) $resource['indice'] = $this->pivot->indice;
        if (isset($this->pivot->indicaciones)) $resource['indicaciones'] = $this->pivot->indicaciones;

        $object = [

            'id' => $this->id,
            'coleccion' => 'event',

            // Datos generales
            'codigo' => $this->codigo,
            'tipo_recurso' => $this->tipoRecurso,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'url_ficha_portal' => $this->urlFichaPortal,

            // Datos generales / datos contacto
            'direccion' => $this->direccion,
            'codigo_postal' => $this->codigoPostal,
            'numero_telefono' => $this->numeroTelefono,
            'email' => $this->email,
            'pagina_web' => $this->paginaWeb,

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

            // Datos agenda
            'subtipo_recurso' => $this->subtipoRecurso,
            'nombre_subtipo_recurso' => $this->nombreSubtipoRecurso,
            'fecha_inicio' => $this->fechaInicio,
            'fecha_fin' => $this->fechaFin,

            // Relaciones
            'imagenes' => ImageResource::collection($this->images)
        ];

        $resource['recurso'] = $object;

        return isset($this->pivot->id) ? $resource : $object;
    }
}
