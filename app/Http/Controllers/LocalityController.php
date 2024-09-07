<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocalityNameResource;
use App\Http\Resources\LocalityResource;
use App\Models\Locality;
use App\Traits\HasFilter;
use App\Traits\HasShow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocalityController extends Controller
{
    use HasFilter, HasShow;

    // Define el modelo para los traits HasShow y HasFilter
    protected function getModel(): string
    {
        return Locality::class;
    }

    // Definir los campos específicos para la búsqueda por términos
    protected function getFieldsToSearch(): array
    {
        return ['nombre', 'descripcion'];
    }

    // Define el recurso detallado para la función show
    protected function getDetailedResourceClass(): string
    {
        return LocalityResource::class;
    }

    // Define el recurso compacto para la función filter
    protected function getCompactResourceClass(): string
    {
        return LocalityResource::class;
    }

    public function names(): AnonymousResourceCollection
    {
        return LocalityNameResource::collection(
            Locality::query()
                ->when(request('idioma'), function (Builder $query) {
                    $query->where('idioma', '=', request('idioma'));
                })
                ->orderBy('nombre')
                ->get());
    }
}
