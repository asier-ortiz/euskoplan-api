<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccommodationResource extends JsonResource
{

    public function toArray($request): array
    {

        // Pivot
        if (isset($this->pivot->id)) $resource['id'] = $this->pivot->id;
        if (isset($this->pivot->indice)) $resource['indice'] = $this->pivot->indice;
        if (isset($this->pivot->indicaciones)) $resource['indicaciones'] = $this->pivot->indicaciones;

        $object = [

            'id' => $this->id,
            'coleccion' => 'accommodation',

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
            'nombre_provincia' => $this->nombreProvincia,
            'nombre_municipio' => $this->nombreMunicipio,

            // Datos generales / georeferenciación
            'longitud' => $this->gmLongitud,
            'latitud' => $this->gmLatitud,

            // Datos alojamiento
            'subtipo_recurso' => $this->subtipoRecurso,
            'nombre_subtipo_recurso' => $this->nombreSubtipoRecurso,
            'categoria' => $this->categoria,
            'capacidad' => $this->capacidad,
            'anno_apertura' => $this->annoApertura,
            'num_hab_individuales' => $this->numHabIndividuales,
            'num_hab_dobles' => $this->numHabDobles,
            'num_hab_salon' => $this->numHabSalon,
            'num_hab_hasta_4_plazas' => $this->numHabHasta4Plazas,
            'num_hab_mas_4_plazas' => $this->numHabMas4Plazas,

            // Relaciones
            'imagenes' => ImageResource::collection($this->images),
            'servicios' => ServiceResource::collection($this->services),
            'precios' => PriceResource::collection($this->prices)

        ];

        $resource['recurso'] = $object;

        return isset($this->pivot->id) ? $resource : $object;
    }
}
