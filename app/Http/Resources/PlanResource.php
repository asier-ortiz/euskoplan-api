<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Request;

class PlanResource extends JsonResource
{

    public function toArray($request): array
    {
        $resources = [
            ['table' => 'accommodations', 'items' => $this->accommodations],
            ['table' => 'caves', 'items' => $this->caves],
            ['table' => 'culturals', 'items' => $this->culturals],
            ['table' => 'events', 'items' => $this->events],
            ['table' => 'fairs', 'items' => $this->fairs],
            ['table' => 'localities', 'items' => $this->localities],
            ['table' => 'museums', 'items' => $this->museums],
            ['table' => 'naturals', 'items' => $this->naturals],
            ['table' => 'restaurants', 'items' => $this->restaurants]
        ];

        $steps = [];

        foreach ($resources as $resource) {
            if (!$resource['items']->isEmpty()) {
                foreach ($resource['items'] as $item) {
                    switch ($resource['table']) {
                        case 'accommodations';
                            $steps[] = AccommodationResource::make($item);
                            break;
                        case 'caves';
                            $steps[] = CaveResource::make($item);
                            break;
                        case 'culturals';
                            $steps[] = CulturalResource::make($item);
                            break;
                        case 'events';
                            $steps[] = EventResource::make($item);
                            break;
                        case 'fairs';
                            $steps[] = FairResource::make($item);
                            break;
                        case 'localities';
                            $steps[] = LocalityResource::make($item);
                            break;
                        case 'museums';
                            $steps[] = MuseumResource::make($item);
                            break;
                        case 'naturals';
                            $steps[] = NaturalResource::make($item);
                            break;
                        case 'restaurants';
                            $steps[] = RestaurantResource::make($item);
                            break;
                    }
                }
            }
        }

        usort($steps, function ($a, $b) {
            return ($a->resource->pivot->indice > $b->resource->pivot->indice) ? 1 : -1;
        });

        if (str_contains('/api/favourite', Request::getUri())) {
            $plan['id'] = $this->favourite->id;
            $object = [
                'id' => $this->id,
                'id_usuario' => $this->user_id,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'votos' => $this->votos,
                'pasos' => $steps
            ];
            $plan['plan'] = $object;
            return $plan;
        }

        $username = User::where('id', '=', $this->user_id)
            ->get('username')
            ->pluck('username')
            ->first();

        return [
            'id' => $this->id,
            'idioma' => $this->idioma,
            'slug' => $this->getRouteKey(),
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'votos' => $this->votos,
            'publico' => $this->publico,
            'pasos' => $steps,
            'id_usuario' => $this->user_id,
            'nombre_usuario' => $username,
        ];
    }
}
