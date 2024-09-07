<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventCompactResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use App\Traits\HasShow;
use Illuminate\Database\Eloquent\Builder;

class EventController extends Controller
{
    use HasCategories, HasFilter, HasShow;

    // Define el modelo para los traits HasShow y HasFilter
    protected function getModel(): string
    {
        return Event::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'nombreSubtipoRecurso', 'descripcion'];
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

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return EventResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return EventCompactResource::class;
    }
}
