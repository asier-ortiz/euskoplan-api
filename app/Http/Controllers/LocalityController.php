<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocalityNameResource;
use App\Http\Resources\LocalityResource;
use App\Models\Locality;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocalityController extends Controller
{

    public function show($code, $language): LocalityResource
    {
        $locality = Locality::where('codigo', '=', $code)->where('idioma', '=', $language)->first();
        return new LocalityResource($locality);
    }

    public function filter(): AnonymousResourceCollection
    {
        return LocalityResource::collection(
            Locality::query()
                ->when(request('idioma'), function (Builder $query) {
                    $query->where('idioma', '=', request('idioma'));
                })
                ->when(request('nombre'), function (Builder $query) {
                    $query->where('nombre', 'like', '%' . request('nombre') . '%');
                })
                ->when(request('nombre_provincia'), function (Builder $query) {
                    $query->where('nombreProvincia', 'like', '%' . request('nombre_provincia') . '%');
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
                ->orderBy('nombre')
                ->get());
    }

    public function search(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda'));

        return LocalityResource::collection(
            Locality::query()
                ->where(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('nombre', 'like', '%' . $term . '%');
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

    public function names(): AnonymousResourceCollection
    {
        return LocalityNameResource::collection(
            Locality::query()
                ->when(request('idioma'), function (Builder $query) {
                    $query->where('idioma', '=', request('idioma'));
                })
                ->orderBy('nombre')
                ->get());
    }

}
