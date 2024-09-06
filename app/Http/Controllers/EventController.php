<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventCompactResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    use HasCategories, HasFilter;

    public function show($code, $language): EventResource
    {
        $event = Event::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new EventResource($event);
    }

    // Aplica los filtros adicionales específicos para eventos (fechas)
    protected function applyAdditionalFilters(Builder $query): void
    {
        $query->when(request('fecha_inicio'), function (Builder $query) {
            $query->whereDate('fechaInicio', '>=', request('fecha_inicio'));
        });

        $query->when(request('fecha_fin'), function (Builder $query) {
            $query->whereDate('fechaFin', '<=', request('fecha_fin'));
        });
    }

    protected function getModel(): string
    {
        return Event::class;
    }

    protected function getResourceClass(): string
    {
        return EventCompactResource::class;
    }
}
