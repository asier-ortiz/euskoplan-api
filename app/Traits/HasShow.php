<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

trait HasShow
{
    public function show($code, $language): JsonResource
    {
        $modelClass = $this->getModel();
        $resourceClass = $this->getDetailedResourceClass(); // Llama al recurso detallado

        $resource = $modelClass::where('codigo', '=', $code)
            ->where('idioma', '=', $language)
            ->firstOrFail();

        return new $resourceClass($resource);
    }

    // Métodos abstractos para definir el modelo y el recurso detallado en cada controlador
    abstract protected function getModel(): string;
    abstract protected function getDetailedResourceClass(): string;
}
