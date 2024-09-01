<?php

namespace App\Http\Controllers;

use App\Http\Resources\FairCompactResource;
use App\Http\Resources\FairResource;
use App\Models\Fair;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class FairController extends Controller
{

    public function show($code, $language): FairResource
    {
        $fair = Fair::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new FairResource($fair);
    }

    public function filter(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda', ''));

        return FairCompactResource::collection(
            Fair::query()
                ->when(request('idioma'), function (Builder $query) {
                    $query->where('idioma', '=', request('idioma'));
                })
                ->when(request('nombre_provincia'), function (Builder $query) {
                    $query->where('nombreProvincia', 'like', '%' . request('nombre_provincia') . '%');
                })
                ->when(request('nombre_municipio'), function (Builder $query) {
                    $query->where('nombreMunicipio', 'like', '%' . request('nombre_municipio') . '%');
                })
                ->when(request('descripcion'), function (Builder $query) {
                    $query->where('descripcion', 'like', '%' . request('descripcion') . '%');
                })
                ->when(request('longitud') && request('latitud') && request('distancia'), function (Builder $query) {
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
                })
                ->when($terms, function (Builder $query) use ($terms) {
                    $query->where(function (Builder $query) use ($terms) {
                        foreach ($terms as $term) {
                            $query->orWhere('nombre', 'like', '%' . $term . '%')
                                ->orWhere('descripcion', 'like', '%' . $term . '%');
                        }
                    });
                })
                ->when(request('aleatorio') === 'si', function (Builder $query) {
                    $query->inRandomOrder();
                })
                ->when(request('limite'), function (Builder $query) {
                    $query->take(request('limite'));
                })
                ->get());
    }

}
