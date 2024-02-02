<?php

namespace App\Http\Controllers;

use App\Http\Resources\NaturalResource;
use App\Models\Natural;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NaturalController extends Controller
{

    public function show($code, $language): NaturalResource
    {
        $natural = Natural::where('codigo', '=', $code)->where('idioma', '=', $language)->first();
        return new NaturalResource($natural);
    }

    public function filter(): AnonymousResourceCollection
    {
        return NaturalResource::collection(
            Natural::query()
                ->when(request('idioma'), function (Builder $query) {
                    $query->where('idioma', '=', request('idioma'));
                })
                ->when(request('nombre'), function (Builder $query) {
                    $query->where('nombre', 'like', '%' . request('nombre') . '%');
                })
                ->when(request('nombre_provincia'), function (Builder $query) {
                    $query->where('nombreProvincia', 'like', '%' . request('nombre_provincia') . '%');
                })
                ->when(request('nombre_municipio'), function (Builder $query) {
                    $query->where('nombreMunicipio', 'like', '%' . request('nombre_municipio') . '%');
                })
                ->when(request('nombre_subtipo_recurso_espacio_natural'), function (Builder $query) {
                    $query->where('nombreSubTipoRecursoEspacioNatural', 'like', '%' . request('nombre_subtipo_recurso_espacio_natural') . '%');
                })
                ->when(request('nombre_subtipo_recurso_playas_pantanos_rios'), function (Builder $query) {
                    $query->where('nombreSubTipoRecursoPlayasPantanosRios', 'like', '%' . request('nombre_subtipo_recurso_playas_pantanos_rios') . '%');
                })
                ->when(request('descripcion'), function (Builder $query) {
                    $query->where('descripcion', 'like', '%' . request('descripcion') . '%');
                })
                ->when(request('aleatorio') === 'si', function (Builder $query) {
                    $query->inRandomOrder();
                })
                ->when(request('limite'), function (Builder $query) {
                    $query->take(request('limite'));
                })
                ->get());
    }

    public function search(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda'));

        return NaturalResource::collection(
            Natural::query()
                ->where(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('nombre', 'like', '%' . $term . '%');
                    }
                })
                ->orWhere(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('nombreSubTipoRecursoEspacioNatural', 'like', '%' . $term . '%');
                    }
                })
                ->orWhere(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('nombreSubTipoRecursoPlayasPantanosRios', 'like', '%' . $term . '%');
                    }
                })
                ->orWhere(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('descripcion', 'like', '%' . $term . '%');
                    }
                })
                ->where('idioma', '=', request('idioma'))
                ->get());
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
