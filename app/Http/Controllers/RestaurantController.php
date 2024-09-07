<?php

namespace App\Http\Controllers;

use App\Http\Resources\RestaurantResource;
use App\Http\Resources\RestaurantCompactResource;
use App\Models\Restaurant;
use App\Traits\HasCategories;
use App\Traits\HasFilter;
use App\Traits\HasShow;

class RestaurantController extends Controller
{
    use HasCategories, HasFilter, HasShow;

    // Define el modelo para el trait HasShow
    protected function getModel(): string
    {
        return Restaurant::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'nombreSubtipoRecurso', 'descripcion'];
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return RestaurantResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return RestaurantCompactResource::class;
    }
}
