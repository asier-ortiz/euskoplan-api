<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class FavouriteResource extends JsonResource
{

    public function toArray($request): array
    {
        $resources = [
            ['table' => 'accommodations', 'items' => $this->favouriteAccommodations],
            ['table' => 'caves', 'items' => $this->favouriteCaves],
            ['table' => 'culturals', 'items' => $this->favouriteCulturals],
            ['table' => 'events', 'items' => $this->favouriteEvents],
            ['table' => 'fairs', 'items' => $this->favouriteFairs],
            ['table' => 'localities', 'items' => $this->favouriteLocalities],
            ['table' => 'museums', 'items' => $this->favouriteMuseums],
            ['table' => 'naturals', 'items' => $this->favouriteNaturals],
            ['table' => 'restaurants', 'items' => $this->favouriteRestaurants],
            ['table' => 'plans', 'items' => $this->favouritePlans],
        ];

        $favourites = [];

        foreach ($resources as $resource) {
            if (!$resource['items']->isEmpty()) {
                foreach ($resource['items'] as $item) {
                    switch ($resource['table']) {
                        case 'accommodations';
                            $favourites['alojamientos'][] = AccommodationResource::make($item);
                            break;
                        case 'caves';
                            $favourites['cuvas_restos_arqueologicos'][] = CaveResource::make($item);
                            break;
                        case 'culturals';
                            $favourites['recursos_culturales'][] = CulturalResource::make($item);
                            break;
                        case 'events';
                            $favourites['eventos'][] = EventResource::make($item);
                            break;
                        case 'fairs';
                            $favourites['parques_tematicos'][] = FairResource::make($item);
                            break;
                        case 'localities';
                            $favourites['localidades'][] = LocalityResource::make($item);
                            break;
                        case 'museums';
                            $favourites['museos_centos_interpretacion'][] = MuseumResource::make($item);
                            break;
                        case 'naturals';
                            $favourites['parques_naturales'][] = NaturalResource::make($item);
                            break;
                        case 'restaurants';
                            $favourites['restaurantes'][] = RestaurantResource::make($item);
                            break;
                        case 'plans';
                            $favourites['planes'][] = PlanResource::make($item);
                            break;
                    }
                }
            }
        }

        $favourites = count($favourites) > 0 ? $favourites : new stdClass();

        return [
            'id_usuario' => $this->id,
            'favoritos' => $favourites
        ];

    }
}
