<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Request;

class PlanCompactResource extends JsonResource
{

    public function toArray($request): array
    {
        $resources = [
            ['table' => 'accommodations', 'items' => $this->accommodations],
            ['table' => 'caves', 'items' => $this->caves],
            ['table' => 'culturals', 'items' => $this->culturals],
            ['table' => 'events', 'items' => $this->events],
            ['table' => 'fairs', 'items' => $this->fairs],
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
                            $steps[] = AccommodationCompactResource::make($item);
                            break;
                        case 'caves';
                            $steps[] = CaveCompactResource::make($item);
                            break;
                        case 'culturals';
                            $steps[] = CulturalCompactResource::make($item);
                            break;
                        case 'events';
                            $steps[] = EventCompactResource::make($item);
                            break;
                        case 'fairs';
                            $steps[] = FairCompactResource::make($item);
                            break;
                        case 'museums';
                            $steps[] = MuseumCompactResource::make($item);
                            break;
                        case 'naturals';
                            $steps[] = NaturalCompactResource::make($item);
                            break;
                        case 'restaurants';
                            $steps[] = RestaurantCompactResource::make($item);
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
            'id_usuario' => $this->user_id,
            'nombre_usuario' => $username,
            'idioma' => $this->idioma,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'slug' => $this->getRouteKey(),
            'votos' => $this->votos,
            'publico' => $this->publico,
            'pasos' => $steps
        ];
    }
}
