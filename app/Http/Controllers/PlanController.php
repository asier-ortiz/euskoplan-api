<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanCreateRequest;
use App\Http\Requests\PlanSuggestRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Http\Resources\PlanCompactResource;
use App\Http\Resources\PlanResource;
use App\Models\Accommodation;
use App\Models\Cave;
use App\Models\Cultural;
use App\Models\Event;
use App\Models\Fair;
use App\Models\Locality;
use App\Models\Museum;
use App\Models\Natural;
use App\Models\Plan;
use App\Models\Restaurant;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Builder;

class PlanController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $query = Plan::query()
            ->where('publico', true)
            ->when(request('idioma'), function (Builder $query) {
                $query->where('idioma', '=', request('idioma'));
            })
            ->when(request('titulo'), function (Builder $query) {
                $query->where('titulo', 'like', '%' . request('titulo') . '%');
            })
            ->when(request('descripcion'), function (Builder $query) {
                $query->where('descripcion', 'like', '%' . request('descripcion') . '%');
            })
            ->when(request('limite'), function (Builder $query) {
                $query->take(request('limite'));
            })
            ->orderBy('votos', 'desc');

        if (request('busqueda')) {
            $terms = explode(' ', request('busqueda'));
            $query->where(function (Builder $q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('titulo', 'like', '%' . $term . '%')
                        ->orWhere('descripcion', 'like', '%' . $term . '%')
                        ->orWhereHas('accommodations', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('caves', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('culturals', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('events', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('fairs', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('localities', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('museums', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('naturals', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        })
                        ->orWhereHas('restaurants', function (Builder $q) use ($term) {
                            $q->where('indicaciones', 'like', '%' . $term . '%');
                        });
                }
            });
        }

        return PlanCompactResource::collection($query->get());
    }

    public function userPlans(): AnonymousResourceCollection
    {
        $user = request()->user();
        return PlanResource::collection(Plan::where('user_id', '=', $user->id)->get());
    }

    public function show($id): \Illuminate\Http\Response|PlanResource|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!$plan || !Gate::allows('read-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        return new PlanResource($plan);
    }

    public function store(PlanCreateRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();
        $plan = Plan::create([
            'idioma' => $validatedData['idioma'],
            'titulo' => $validatedData['titulo'],
            'descripcion' => $validatedData['descripcion'],
            'publico' => $validatedData['publico'],
            'user_id' => request()->user()->id
        ]);

        StepController::bulkStore($validatedData['pasos'], $plan->id);
        return response(new PlanResource($plan), Response::HTTP_CREATED);
    }

    public function update(PlanUpdateRequest $request, $id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!Gate::allows('update-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        $validatedData = $request->validated();
        $plan->update($validatedData);
        return response(new PlanResource($plan), Response::HTTP_ACCEPTED);
    }

    public function destroy($id): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $plan = Plan::find($id);
        if (!Gate::allows('destroy-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }
        $plan->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function upvote($id): void
    {
        $plan = Plan::find($id);
        $plan->increment('votos');
    }

    public function downvote($id): void
    {
        $plan = Plan::find($id);
        $plan->decrement('votos');
    }

    public function route($id, $profile): mixed
    {
        $plan = Plan::find($id);
        if (!$plan || !Gate::allows('read-plan', $plan)) {
            return response(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $planResource = new PlanResource($plan);
        $steps = json_decode($planResource->toJson())->pasos;
        if (count($steps) < 2) {
            return response(['error' => 'Not enough plan steps'], Response::HTTP_BAD_REQUEST);
        }

        $coordinatesString = implode(';', array_map(fn($step) => $step->recurso->longitud . ',' . $step->recurso->latitud, $steps));

        $token = env('MAP_BOX_TOKEN');
        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/$profile/$coordinatesString?alternatives=false&geometries=geojson&language=es&overview=simplified&steps=false&access_token=$token");
        return $response->json();
    }

    public function suggestItinerary(PlanSuggestRequest $request): \Illuminate\Http\Response|JsonResponse|Application|ResponseFactory
    {
        $validatedData = $request->validated();

        $province = $validatedData['provincia'];
        $month = $validatedData['mes'];
        $year = $validatedData['año'];
        $days = $validatedData['dias'];
        $tripType = $validatedData['tipo_viaje'];

        $cacheKey = 'itinerary_' . md5($province . $month . $year . $days . $tripType);

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey), Response::HTTP_OK);
        }

        $filteredPlaces = collect();

        if ($tripType == 'cultura') {
            $filteredPlaces = Cultural::where('nombreProvincia', $province)->get();
            $filteredPlaces = $filteredPlaces->merge(Museum::where('nombreProvincia', $province)->get());
            $filteredPlaces = $filteredPlaces->merge(Locality::where('nombreProvincia', $province)->get());
            $filteredPlaces = $filteredPlaces->merge(Event::where('nombreProvincia', $province)
                ->whereMonth('fechaInicio', '=', $month)
                ->whereYear('fechaInicio', '=', $year)
                ->whereIn('nombreSubtipoRecurso', [
                    'Conciertos', 'Danza y Teatro', 'Exposiciones',
                    'Festivales', 'Fiestas y Tradiciones', 'Visitas y rutas guiadas'
                ])->get());
        } elseif ($tripType == 'aventura') {
            $filteredPlaces = Natural::where('nombreProvincia', $province)->get();
            $filteredPlaces = $filteredPlaces->merge(Cave::where('nombreProvincia', $province)->get());
            $filteredPlaces = $filteredPlaces->merge(Event::where('nombreProvincia', $province)
                ->whereMonth('fechaInicio', '=', $month)
                ->whereYear('fechaInicio', '=', $year)
                ->whereIn('nombreSubtipoRecurso', [
                    'Deportes', 'Visitas y rutas guiadas', 'Festivales', 'Otros'
                ])->get());
        } elseif ($tripType == 'familiar') {
            $filteredPlaces = Fair::where('nombreProvincia', $province)->get();
            $filteredPlaces = $filteredPlaces->merge(Natural::where('nombreProvincia', $province)->get());
            $filteredPlaces = $filteredPlaces->merge(Museum::where('nombreProvincia', $province)->get());
            $filteredPlaces = $filteredPlaces->merge(Event::where('nombreProvincia', $province)
                ->whereMonth('fechaInicio', '=', $month)
                ->whereYear('fechaInicio', '=', $year)
                ->whereIn('nombreSubtipoRecurso', [
                    'Actividades familiares', 'Eventos gastronómicos',
                    'Fiestas y Tradiciones', 'Festivales'
                ])->get());
        }

        // Distribuir actividades en bloques de mañana, tarde y noche
        $morningActivities = $filteredPlaces->take($days * 2);
        $afternoonActivities = $filteredPlaces->skip($days * 2)->take($days * 2);
        $nightActivities = $filteredPlaces->skip($days * 4)->take($days);

        // Calcular el "centro" de las coordenadas de las actividades de la mañana
        $morningCenterLatitude = $morningActivities->avg('gmLatitud');
        $morningCenterLongitude = $morningActivities->avg('gmLongitud');

        // Filtrar alojamientos y restaurantes cercanos a las actividades de la mañana
        $accommodations = Accommodation::where('nombreProvincia', $province)
            ->where(function ($query) use ($tripType) {
                if ($tripType == 'cultura') {
                    $query->whereIn('nombreSubtipoRecurso', ['Hotel', 'Pensión']);
                } elseif ($tripType == 'aventura') {
                    $query->whereIn('nombreSubtipoRecurso', ['Camping', 'Casa Rural']);
                } elseif ($tripType == 'familiar') {
                    $query->whereIn('nombreSubtipoRecurso', ['Agroturismo', 'Apartamento']);
                }
            })
            ->orderByRaw("(6371 * acos(cos(radians($morningCenterLatitude)) * cos(radians(gmLatitud)) * cos(radians(gmLongitud) - radians($morningCenterLongitude)) + sin(radians($morningCenterLatitude)) * sin(radians(gmLatitud)))) ASC")
            ->take(5)
            ->get();

        $restaurants = Restaurant::where('nombreProvincia', $province)
            ->where(function ($query) use ($tripType) {
                if ($tripType == 'cultura') {
                    $query->whereIn('nombreSubtipoRecurso', ['Restaurante', 'Bodegas de Vino']);
                } elseif ($tripType == 'aventura') {
                    $query->whereIn('nombreSubtipoRecurso', ['Sidrería', 'Asador']);
                } elseif ($tripType == 'familiar') {
                    $query->whereIn('nombreSubtipoRecurso', ['Pastelerías / Confiterías', 'Restaurante']);
                }
            })
            ->orderByRaw("(6371 * acos(cos(radians($morningCenterLatitude)) * cos(radians(gmLatitud)) * cos(radians(gmLongitud) - radians($morningCenterLongitude)) + sin(radians($morningCenterLatitude)) * sin(radians(gmLatitud)))) ASC")
            ->take(5)
            ->get();

        // Preparar los recursos para la API
        $placesForApi = $morningActivities->merge($afternoonActivities)->merge($nightActivities)->map(function ($place) {
            return [
                'id' => $place->id,
                'type' => get_class($place),
                'name' => $place->nombre,
                'province' => $place->nombreProvincia,
                'latitude' => $place->gmLatitud,
                'longitude' => $place->gmLongitud
            ];
        });

        $accommodationsForApi = $accommodations->map(function ($accommodation) {
            return [
                'id' => $accommodation->id,
                'type' => get_class($accommodation),
                'name' => $accommodation->nombre,
                'province' => $accommodation->nombreProvincia,
                'latitude' => $accommodation->gmLatitud,
                'longitude' => $accommodation->gmLongitud
            ];
        });

        $restaurantsForApi = $restaurants->map(function ($restaurant) {
            return [
                'id' => $restaurant->id,
                'type' => get_class($restaurant),
                'name' => $restaurant->nombre,
                'province' => $restaurant->nombreProvincia,
                'latitude' => $restaurant->gmLatitud,
                'longitude' => $restaurant->gmLongitud
            ];
        });

        // Crear el prompt para OpenAI
        $prompt = 'Genera un itinerario de ' . $days . ' días basado en estos datos.
       Incluye el `planables_id` y `planables_type` para cada recurso utilizado en el itinerario.

    Devuelve la respuesta en formato JSON con la estructura:
    [
        {
            "indice": número del paso,
            "dia": número del día,
            "indicaciones": "descripción detallada del paso",
            "planables_id": id del recurso utilizado,
            "planables_type": "tipo del recurso utilizado"
        },
        ...
    ]

    Aquí tienes los recursos disponibles:
    Lugares de interés: ' . json_encode($placesForApi->toArray()) . ',
    Alojamientos: ' . json_encode($accommodationsForApi->toArray()) . ',
    Restaurantes: ' . json_encode($restaurantsForApi->toArray()) . '.';

        dd($prompt);

        $openAiApiKey = env('OPENAI_API_KEY');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $openAiApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/completions', [
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 1500,
        ]);

        if ($response->successful()) {
            $itinerary = $response->json()['choices'][0]['text'];

            $steps = json_decode($itinerary, true);

            $tempPlan = [
                'language' => 'es',
                'title' => 'Itinerario sugerido para ' . $province,
                'description' => $itinerary,
                'public' => false,
                'user_id' => $request->user()->id,
                'steps' => $steps,
            ];

            Cache::put($cacheKey, $tempPlan, 60 * 60 * 24);

            // TODO Hacer que devuelva un PlanResource

            return response()->json($tempPlan, Response::HTTP_OK);
        } else {
            return response(['error' => 'Error al generar el itinerario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
