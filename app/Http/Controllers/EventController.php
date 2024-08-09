<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

class EventController extends Controller
{

    public function show($code, $language): EventResource
    {
        $event = Event::where('codigo', '=', $code)->where('idioma', '=', $language)->first();
        return new EventResource($event);
    }

    public function filter(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda', ''));

        return EventResource::collection(
            Event::query()
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
                ->when(request('fecha_inicio'), function (Builder $query) {
                    $query->whereDate('fechaInicio', '>=', request('fecha_inicio'));
                })
                ->when(request('fecha_fin'), function (Builder $query) {
                    $query->whereDate('fechaFin', '<=', request('fecha_fin'));
                })
                ->when(request('descripcion'), function (Builder $query) {
                    $query->where('descripcion', 'like', '%' . request('descripcion') . '%');
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

    public function categories($language): Collection
    {
        return Event::select('nombreSubtipoRecurso as nombre_subtipo_recurso')
            ->whereNotNull('nombreSubtipoRecurso')
            ->where('idioma', '=', $language)
            ->distinct()
            ->orderBy('nombreSubtipoRecurso')
            ->get();
    }

}
