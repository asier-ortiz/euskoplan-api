<?php

namespace App\Http\Controllers;

use App\Http\Resources\FairCompactResource;
use App\Http\Resources\FairResource;
use App\Models\Fair;
use App\Traits\HasFilter;
use App\Traits\HasShow;

class FairController extends Controller
{
    use HasFilter, HasShow;

    // Define el modelo para los traits HasShow y HasFilter
    protected function getModel(): string
    {
        return Fair::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'descripcion'];
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return FairResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return FairCompactResource::class;
    }
}
