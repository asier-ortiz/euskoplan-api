<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait HasFilter
{
    public function filter(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda', ''));

        // Usa la función 'getModel' y 'getCompactResourceClass' definidas en el controlador
        $resourceClass = $this->getCompactResourceClass();
        $query = $this->getModel()::query();

        // Campos comunes en todos los controladores
        $query->when(request('idioma'), function (Builder $query) {
            $query->where('idioma', '=', request('idioma'));
        });

        $query->when(request('nombre_provincia'), function (Builder $query) {
            $query->where('nombreProvincia', 'like', '%' . request('nombre_provincia') . '%');
        });

        $query->when(request('nombre_municipio'), function (Builder $query) {
            $query->where('nombreMunicipio', 'like', '%' . request('nombre_municipio') . '%');
        });

        $query->when(request('nombre_subtipo_recurso'), function (Builder $query) {
            $query->where('nombreSubtipoRecurso', 'like', '%' . request('nombre_subtipo_recurso') . '%');
        });

        $query->when(request('descripcion'), function (Builder $query) {
            $query->where('descripcion', 'like', '%' . request('descripcion') . '%');
        });

        $query->when(request('longitud') && request('latitud') && request('distancia'), function (Builder $query) {
            $haversine = "(6371 * acos(cos(radians(?))
                        * cos(radians(gmLatitud))
                        * cos(radians(gmLongitud) - radians(?))
                        + sin(radians(?))
                        * sin(radians(gmLatitud))))";

            $query->whereRaw("$haversine < ?", [
                request('latitud'),
                request('longitud'),
                request('latitud'),
                request('distancia')
            ]);
        });

        // Aplica filtros adicionales específicos del controlador
        $this->applyAdditionalFilters($query);

        // Definir los campos que se usarán para buscar términos en el controlador
        $fieldsToSearch = $this->getFieldsToSearch();

        // Filtro por términos en los campos definidos
        $query->when($terms, function (Builder $query) use ($terms, $fieldsToSearch) {
            $query->where(function (Builder $query) use ($terms, $fieldsToSearch) {
                foreach ($terms as $term) {
                    foreach ($fieldsToSearch as $field) {
                        $query->orWhere($field, 'like', '%' . $term . '%');
                    }
                }
            });
        });

        $query->when(request('aleatorio') === 'si', function (Builder $query) {
            $query->inRandomOrder();
        });

        $query->when(request('limite'), function (Builder $query) {
            $query->take(request('limite'));
        });

        return $resourceClass::collection($query->get());
    }

    // Métodos abstractos que deben ser implementados en el controlador
    abstract protected function getCompactResourceClass();
    abstract protected function getModel();
    abstract protected function getFieldsToSearch(): array;

    // Función para aplicar filtros adicionales, que puede ser sobreescrita
    protected function applyAdditionalFilters(Builder $query)
    {
        // Filtros adicionales por defecto, si se necesitan
    }

}
