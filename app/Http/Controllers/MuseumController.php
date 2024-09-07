<?php

namespace App\Http\Controllers;

use App\Http\Resources\MuseumCompactResource;
use App\Http\Resources\MuseumResource;
use App\Models\Museum;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use App\Traits\HasShow;

class MuseumController extends Controller
{
    use HasCategories, HasFilter, HasShow;

    // Define el modelo para los traits HasShow y HasFilter
    protected function getModel(): string
    {
        return Museum::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'nombreSubtipoRecurso', 'descripcion'];
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return MuseumResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return MuseumCompactResource::class;
    }
}
