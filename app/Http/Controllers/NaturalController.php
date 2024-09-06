<?php

namespace App\Http\Controllers;

use App\Http\Resources\NaturalCompactResource;
use App\Http\Resources\NaturalResource;
use App\Models\Natural;
use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NaturalController extends Controller
{
    use HasFilter;

    public function show($code, $language): NaturalResource
    {
        $natural = Natural::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new NaturalResource($natural);
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

    protected function getModel(): string
    {
        return Natural::class;
    }

    protected function getResourceClass(): string
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
