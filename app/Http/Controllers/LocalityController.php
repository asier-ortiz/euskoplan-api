<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocalityNameResource;
use App\Http\Resources\LocalityResource;
use App\Models\Locality;
use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocalityController extends Controller
{
    use HasFilter;

    public function show($code, $language): LocalityResource
    {
        $locality = Locality::where('codigo', '=', $code)->where('idioma', '=', $language)->firstOrFail();
        return new LocalityResource($locality);
    }

    protected function getModel(): string
    {
        return Locality::class;
    }

    protected function getResourceClass(): string
    {
        return LocalityResource::class;
    }

    public function search(): AnonymousResourceCollection
    {
        $terms = explode(" ", request('busqueda'));

        return LocalityResource::collection(
            Locality::query()
                ->where(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('nombre', 'like', '%' . $term . '%');
                    }
                })
                ->orWhere(function (Builder $query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where('descripcion', 'like', '%' . $term . '%');
                    }
                })
                ->where('idioma', '=', request('idioma'))
                ->get());
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
