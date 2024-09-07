<?php

namespace App\Http\Controllers;

use App\Http\Resources\CulturalCompactResource;
use App\Http\Resources\CulturalResource;
use App\Models\Cultural;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use App\Traits\HasShow;

class CulturalController extends Controller
{
    use HasCategories, HasFilter, HasShow;

    // Define el modelo para los traits HasShow y HasFilter
    protected function getModel(): string
    {
        return Cultural::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'nombreSubtipoRecurso', 'descripcion'];
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return CulturalResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return CulturalCompactResource::class;
    }
}
