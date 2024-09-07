<?php

namespace App\Http\Controllers;

use App\Http\Resources\NaturalCompactResource;
use App\Http\Resources\NaturalResource;
use App\Models\Natural;
use App\Traits\HasFilter;
use App\Traits\HasShow;
use Illuminate\Database\Eloquent\Builder;

class NaturalController extends Controller
{
    use HasFilter, HasShow;

    // Define el modelo para los traits HasShow y HasFilter
    protected function getModel(): string
    {
        return Natural::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'nombreSubTipoRecursoEspacioNatural', 'nombreSubTipoRecursoPlayasPantanosRios', 'descripcion'];
    }

    // Aplica los filtros adicionales específicos para recursos naturales
    protected function applyAdditionalFilters(Builder $query): void
    {
        $query->when(request('nombre_subtipo_recurso_espacio_natural'), function (Builder $query) {
            $query->where('nombreSubTipoRecursoEspacioNatural', 'like', '%' . request('nombre_subtipo_recurso_espacio_natural') . '%');
        });

        $query->when(request('nombre_subtipo_recurso_playas_pantanos_rios'), function (Builder $query) {
            $query->where('nombreSubTipoRecursoPlayasPantanosRios', 'like', '%' . request('nombre_subtipo_recurso_playas_pantanos_rios') . '%');
        });
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return NaturalResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return NaturalCompactResource::class;
    }

    public function categories($language): array
    {
        $categories = [];

        $categories['espacio_natural'] = Natural::select('nombreSubTipoRecursoEspacioNatural as nombre_subtipo_recurso_espacio_natural')
            ->whereNotNull('nombreSubTipoRecursoEspacioNatural')
            ->where('idioma', '=', $language)
            ->distinct()
            ->orderBy('nombreSubTipoRecursoEspacioNatural')
            ->get();

        $categories['playas_pantanos_rios'] = Natural::select('nombreSubTipoRecursoPlayasPantanosRios as nombre_subtipo_recurso_playas_pantanos_rios')
            ->whereNotNull('nombreSubTipoRecursoPlayasPantanosRios')
            ->where('idioma', '=', $language)
            ->distinct()
            ->orderBy('nombreSubTipoRecursoPlayasPantanosRios')
            ->get();

        return $categories;
    }
}
