<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait HasCategories
{
    public function categories($language): Collection
    {
        $model = $this->getModel();

        return $model::select('nombreSubtipoRecurso as nombre_subtipo_recurso')
            ->whereNotNull('nombreSubtipoRecurso')
            ->where('idioma', '=', $language)
            ->distinct()
            ->orderBy('nombreSubtipoRecurso')
            ->get();
    }
    
    abstract protected function getModel();
}
