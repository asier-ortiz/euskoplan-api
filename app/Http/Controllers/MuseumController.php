<?php

namespace App\Http\Controllers;

use App\Http\Resources\MuseumCompactResource;
use App\Http\Resources\MuseumResource;
use App\Models\Museum;
use App\Traits\HasCategories;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MuseumController extends Controller
{
    use HasCategories;

    public function show($code, $language): MuseumResource
    {
        $museum = Museum::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new MuseumResource($museum);
    }

    public function filter(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda', ''));

        return MuseumCompactResource::collection(
            Museum::query()
                ->when(request('idioma'), function (Builder $query) {
                    $query->where('idioma', '=', request('idioma'));
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
                                ->orWhere('nombreSubtipoRecurso', 'like', '%' . $term . '%')
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

    protected function getModel(): string
    {
        return Museum::class;
    }

}
