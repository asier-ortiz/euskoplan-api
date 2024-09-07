<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccommodationResource;
use App\Http\Resources\AccommodationCompactResource;
use App\Models\Accommodation;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use App\Traits\HasShow;

class AccommodationController extends Controller
{
    use HasCategories, HasFilter, HasShow;

    // Define el modelo para el trait HasShow y HasFilter
    protected function getModel(): string
    {
        return Accommodation::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'nombreSubtipoRecurso', 'descripcion'];
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return AccommodationResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return AccommodationCompactResource::class;
    }
}
