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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $plan = Plan::findOrFail($id);
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
        // Obtiene el plan y verifica permisos
        $plan = Plan::find($id);
        if (!$plan || !Gate::allows('read-plan', $plan)) {
            return response(['error' => 'Prohibido'], Response::HTTP_FORBIDDEN);
        }

        // Obtiene los pasos del plan
        $planResource = new PlanResource($plan);
        $steps = json_decode($planResource->toJson())->pasos;
        if (count($steps) < 2) {
            return response(['error' => 'No hay suficientes pasos en el plan'], Response::HTTP_BAD_REQUEST);
        }

        // Genera una cadena de coordenadas basada en los pasos
        $coordinatesString = implode(';', array_map(fn($step) => $step->recurso->longitud . ',' . $step->recurso->latitud, $steps));

        // Genera una clave de caché única basada en el ID del plan, el perfil y las coordenadas
        $cacheKey = 'map_route_' . md5($id . $profile . $coordinatesString);

        // Verifica si la ruta ya está almacenada en caché
        $cachedRoute = Cache::store('redis')->get($cacheKey);
        if ($cachedRoute) {
            return response()->json($cachedRoute);
        }

        // Si no está en caché, realiza la solicitud a la API de Mapbox
        $token = env('MAP_BOX_TOKEN');
        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/$profile/$coordinatesString?alternatives=false&geometries=geojson&language=es&overview=simplified&steps=false&access_token=$token");

        // Verifica si la respuesta de la API fue exitosa
        if ($response->successful()) {
            $routeData = $response->json();

            // Almacena en caché los datos de la ruta por 7 días (60 * 60 * 24 * 7 segundos)
            Cache::store('redis')->put($cacheKey, $routeData, 60 * 60 * 24 * 7);

            return response()->json($routeData);
        } else {
            // Maneja el caso de error si la solicitud a la API falla
            return response()->json(['error' => 'Error al obtener la ruta de Mapbox'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function suggestItinerary(PlanSuggestRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $province = $validatedData['provincia'];
        $month = $validatedData['mes'];
        $year = $validatedData['año'];
        $days = $validatedData['dias'];
        $tripType = $validatedData['tipo_viaje'];
        $language = $validatedData['idioma'];

        $userId = $request->user()->id;
        $cacheKey = 'itinerary_' . md5($province . $month . $year . $days . $tripType . $language);
        $userViewedKey = 'user_' . $userId . '_viewed_' . $cacheKey;

        // Obtener itinerarios cacheados y los itinerarios que el usuario ya ha visto
        $cachedItineraries = Cache::store('redis')->get($cacheKey, []);
        $userViewedItineraries = Cache::store('redis')->get($userViewedKey, []);

        // Filtrar los itinerarios no vistos por el usuario
        $unseenItineraries = array_diff_key($cachedItineraries, array_flip($userViewedItineraries));

        if (!empty($unseenItineraries)) {
            // Devolver un itinerario no visto por el usuario
            $itinerary = reset($unseenItineraries);
            $userViewedItineraries[] = $itinerary['hash']; // Guardar que este itinerario ya fue visto
            Cache::store('redis')->put($userViewedKey, $userViewedItineraries, 60 * 60 * 24);
            return response()->json($itinerary['data'], Response::HTTP_OK);
        }

        // Si todos los itinerarios cacheados ya fueron vistos o no hay itinerarios en caché
        $data = $this->prepareItineraryData($province, $month, $year, $days, $tripType, $language);

        // Genera los mensajes para el servicio de OpenAI
        $messages = $this->generateItineraryMessages($days, $data, $language);

        $openAiApiKey = env('OPENAI_API_KEY');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $openAiApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'max_tokens' => 2_000,
        ]);

        if ($response->successful()) {
            $itineraryContent = $response->json()['choices'][0]['message']['content'];

            // Limpieza del contenido JSON
            $cleanedItinerary = trim($itineraryContent);
            $cleanedItinerary = preg_replace('/```json/', '', $cleanedItinerary);
            $cleanedItinerary = preg_replace('/```/', '', $cleanedItinerary);

            $decodedResponse = json_decode($cleanedItinerary, true);

            if ($decodedResponse === null || !isset($decodedResponse['title']) || !isset($decodedResponse['description']) || !isset($decodedResponse['steps'])) {
                return response()->json([
                    'error' => 'El JSON decodificado no tiene la estructura esperada.',
                    'decoded_response' => $decodedResponse,
                    'raw_response' => $cleanedItinerary
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            foreach ($decodedResponse['steps'] as &$step) {
                $resource = $this->fetchResource($step['resource_id'], $step['planables_type']);
                if ($resource) {
                    $step['resource'] = $resource; // Añade la información del recurso al paso
                }
            }

            $itineraryHash = md5(json_encode($decodedResponse)); // Hash del contenido del itinerario
            $tempPlan = [
                'hash' => $itineraryHash,
                'data' => [
                    'language' => $language,
                    'title' => $decodedResponse['title'],
                    'description' => $decodedResponse['description'],
                    'public' => false,
                    'user_id' => $userId,
                    'steps' => $decodedResponse['steps'],
                ]
            ];

            // Cachear el nuevo resultado
            $cachedItineraries[$itineraryHash] = $tempPlan;
            Cache::store('redis')->put($cacheKey, $cachedItineraries, 60 * 60 * 24);

            // Añadir a la lista de itinerarios vistos por el usuario
            $userViewedItineraries[] = $itineraryHash;
            Cache::store('redis')->put($userViewedKey, $userViewedItineraries, 60 * 60 * 24);

            return response()->json($tempPlan['data'], Response::HTTP_OK);

        } else {
            return response()->json([
                'error' => 'Error al comunicarse con el servicio de generación de itinerarios.',
                'api_response' => $response->json()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function prepareItineraryData($province, $month, $year, $days, $tripType, $language): array
    {
        $filteredPlaces = collect();

        if ($tripType == 'cultura') {
            // Prioridad a eventos
            $events = Event::where('nombreProvincia', $province)
                ->whereMonth(DB::raw('DATE(fechaInicio)'), '=', $month)
                ->whereYear(DB::raw('DATE(fechaInicio)'), '=', $year)
                ->where('idioma', $language)
                ->whereIn('nombreSubtipoRecurso', [
                    'Conciertos', 'Danza y Teatro', 'Exposiciones',
                    'Festivales', 'Fiestas y Tradiciones', 'Visitas y rutas guiadas'
                ])->get();

            // Resto de lugares
            $culturalPlaces = Cultural::where('nombreProvincia', $province)->where('idioma', $language)->get();
            $museums = Museum::where('nombreProvincia', $province)->where('idioma', $language)->get();
            $localities = Locality::where('nombreProvincia', $province)->where('idioma', $language)->get();

            // Ponderación: duplicar los eventos para darles mayor prioridad
            $events = $events->merge($events);

            // Mezclar los eventos con el resto, manteniendo prioridad
            $filteredPlaces = $filteredPlaces->merge($events)
                ->merge($culturalPlaces)
                ->merge($museums)
                ->merge($localities);

        } elseif ($tripType == 'aventura') {
            $events = Event::where('nombreProvincia', $province)
                ->whereMonth(DB::raw('DATE(fechaInicio)'), '=', $month)
                ->whereYear(DB::raw('DATE(fechaInicio)'), '=', $year)
                ->where('idioma', $language)
                ->whereIn('nombreSubtipoRecurso', [
                    'Deportes', 'Visitas y rutas guiadas', 'Festivales', 'Otros'
                ])->get();

            $naturalPlaces = Natural::where('nombreProvincia', $province)->where('idioma', $language)->get();
            $caves = Cave::where('nombreProvincia', $province)->where('idioma', $language)->get();

            $events = $events->merge($events);

            $filteredPlaces = $filteredPlaces->merge($events)
                ->merge($naturalPlaces)
                ->merge($caves);

        } elseif ($tripType == 'familiar') {
            $events = Event::where('nombreProvincia', $province)
                ->whereMonth(DB::raw('DATE(fechaInicio)'), '=', $month)
                ->whereYear(DB::raw('DATE(fechaInicio)'), '=', $year)
                ->where('idioma', $language)
                ->whereIn('nombreSubtipoRecurso', [
                    'Actividades familiares', 'Eventos gastronómicos',
                    'Fiestas y Tradiciones', 'Festivales'
                ])->get();

            $fairs = Fair::where('nombreProvincia', $province)->where('idioma', $language)->get();
            $naturalPlaces = Natural::where('nombreProvincia', $province)->where('idioma', $language)->get();
            $museums = Museum::where('nombreProvincia', $province)->where('idioma', $language)->get();

            $events = $events->merge($events);

            $filteredPlaces = $filteredPlaces->merge($events)
                ->merge($fairs)
                ->merge($naturalPlaces)
                ->merge($museums);
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
            ->where('idioma', $language)
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
            ->where('idioma', $language)
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

        return [
            'places' => $morningActivities->merge($afternoonActivities)->merge($nightActivities)->map(function ($place) {
                return [
                    'id' => $place->id,
                    'type' => get_class($place),
                    'name' => $place->nombre,
                    'description' => $place->descripcion,
                    'province' => $place->nombreProvincia,
                    'latitude' => $place->gmLatitud,
                    'longitude' => $place->gmLongitud
                ];
            }),
            'accommodations' => $accommodations->map(function ($accommodation) {
                return [
                    'id' => $accommodation->id,
                    'type' => get_class($accommodation),
                    'name' => $accommodation->nombre,
                    'description' => $accommodation->descripcion,
                    'province' => $accommodation->nombreProvincia,
                    'latitude' => $accommodation->gmLatitud,
                    'longitude' => $accommodation->gmLongitud
                ];
            }),
            'restaurants' => $restaurants->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'type' => get_class($restaurant),
                    'name' => $restaurant->nombre,
                    'description' => $restaurant->descripcion,
                    'province' => $restaurant->nombreProvincia,
                    'latitude' => $restaurant->gmLatitud,
                    'longitude' => $restaurant->gmLongitud
                ];
            })
        ];
    }

    private function fetchResource($resourceId, $planablesType): array|Model|Cultural|Collection|Event|Natural|Accommodation|Locality|Cave|Fair|null
    {
        return match ($planablesType) {
            'accommodation' => Accommodation::find($resourceId),
            'cave' => Cave::find($resourceId),
            'cultural' => Cultural::find($resourceId),
            'event' => Event::find($resourceId),
            'fair' => Fair::find($resourceId),
            'locality' => Locality::find($resourceId),
            'museum' => Museum::find($resourceId),
            'natural' => Natural::find($resourceId),
            'restaurant' => Restaurant::find($resourceId),
            default => null,
        };
    }

    private function generateItineraryMessages($days, $data, $language): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant that generates detailed itineraries based on provided data.'
            ],
            [
                'role' => 'user',
                'content' => ($language === 'en' ?
                    'Generate a ' . $days . '-day itinerary based on this data.
                Include the `resource_id` and `planables_type` for each resource used in the itinerary.
                Respond in English.
                Format the response as JSON (only JSON) with the following structure:

                "title": Short title of the itinerary,
                "description": Short general description of the itinerary,
                "steps": [
                    {
                        "indice": Step number,
                        "dia": Day number,
                        "indicaciones": "Detailed step description",
                        "resource_id": ID of the resource used,
                        "planables_type": Type of the resource (collection name in lowercase, e.g., accommodation, cave, cultural, etc.)"
                    },
                    ...
                ]

                Here are the available resources:
                Points of interest: ' . json_encode($data['places']->toArray()) . ',
                Accommodations: ' . json_encode($data['accommodations']->toArray()) . ',
                Restaurants: ' . json_encode($data['restaurants']->toArray()) . '.' :

                    'Genera un itinerario de ' . $days . ' días basado en estos datos.
                Incluye el `resource_id` y `planables_type` para cada recurso utilizado en el itinerario.
                Devuelve la respuesta en español.
                Devuelve la respuesta en formato JSON (Sólo el JSON) con la estructura:

                "title": Título corto del itinerario,
                "description": Descripción general del itinerario completo en un párrafo corto,
                "steps": [
                    {
                        "indice": Número del paso,
                        "dia": Número del día,
                        "indicaciones": "Descripción detallada del paso",
                        "resource_id": ID del recurso utilizado,
                        "planables_type": Tipo del recurso (nombre de la colección en minúsculas)"
                    },
                    ...
                ]

                Aquí tienes los recursos disponibles:
                Lugares de interés: ' . json_encode($data['places']->toArray()) . ',
                Alojamientos: ' . json_encode($data['accommodations']->toArray()) . ',
                Restaurantes: ' . json_encode($data['restaurants']->toArray()) . '.'
                )
            ]
        ];
    }

}
