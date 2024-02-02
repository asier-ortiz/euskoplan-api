<?php

namespace App\Http\Controllers;

use App\Http\Resources\CaveResource;
use App\Models\Cave;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class CaveController extends Controller
{

    public function show($code, $language): CaveResource
    {
        $cave = Cave::where('codigo', '=', $code)->where('idioma', '=', $language)->first();
        return new CaveResource($cave);
    }

    public function filter(): AnonymousResourceCollection
    {
        return CaveResource::collection(
            Cave::query()
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
                ->when(request('nombre_subtipo_recurso'), function (Builder $query) {
                    $query->where('nombreSubtipoRecurso', 'like', '%' . request('nombre_subtipo_recurso') . '%');
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

        return CaveResource::collection(
            Cave::query()
                ->where(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('nombre', 'like', '%' . $term . '%');
                    }
                })
                ->orWhere(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('nombreSubtipoRecurso', 'like', '%' . $term . '%');
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

    public function categories($language): Collection
    {
        return Cave::select('nombreSubtipoRecurso as nombre_subtipo_recurso')
            ->whereNotNull('nombreSubtipoRecurso')
            ->where('idioma', '=', $language)
            ->distinct()
            ->orderBy('nombreSubtipoRecurso')
            ->get();
    }
}
