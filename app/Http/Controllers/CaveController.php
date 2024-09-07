<?php

namespace App\Http\Controllers;

use App\Http\Resources\CaveCompactResource;
use App\Http\Resources\CaveResource;
use App\Models\Cave;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use App\Traits\HasShow;

class CaveController extends Controller
{
    use HasCategories, HasFilter, HasShow;

    // Define el modelo para los traits HasShow y HasFilter
    protected function getModel(): string
    {
        return Cave::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'nombreSubtipoRecurso', 'descripcion'];
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return CaveResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return CaveCompactResource::class;
    }
}
