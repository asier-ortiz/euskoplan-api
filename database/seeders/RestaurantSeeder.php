<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Restaurant;
use App\Models\Service;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{

    public function run(): void
    {
        $language = config('app.seeder_language', 'es');
        $path = database_path() . '/data/' . $language . '-restaurants.json';
        $json = file_get_contents($path);
        $jsonData = json_decode($json, false);

        foreach ($jsonData as $obj) {

            if (isset($obj->fechaActualizacion)) {

                // Creación de los restaurantes
                $restaurant = Restaurant::updateOrCreate([

                    'fechaActualizacion' => $obj->fechaActualizacion,
                    'idioma' => $language,

                    // Datos generales
                    'codigo' => $obj->datosGenerales->codigo ?? null,
                    'tipoRecurso' => $obj->datosGenerales->tipoRecurso ?? null,
                    'nombre' => $obj->datosGenerales->nombre ?? null,
                    'descripcion' => $obj->datosGenerales->descripcion ?? null,
                    'urlFichaPortal' => $obj->datosGenerales->urlFichaPortal ?? null,

                    // Datos generales / datos contacto
                    'direccion' => $obj->datosGenerales->datosContacto->direccion ?? null,
                    'codigoPostal' => $obj->datosGenerales->datosContacto->codigoPostal ?? null,
                    'numeroTelefono' => $obj->datosGenerales->datosContacto->numeroTelefono ?? null,
                    'numeroTelefono2' => $obj->datosGenerales->datosContacto->numeroTelefono2 ?? null,
                    'email' => $obj->datosGenerales->datosContacto->email ?? null,
                    'paginaWeb' => $obj->datosGenerales->datosContacto->paginaWeb ?? null,

                    // Datos generales / localización
                    'codigoProvincia' => $obj->datosGenerales->localizacion->codigoProvincia ?? null,
                    'codigoMunicipio' => $obj->datosGenerales->localizacion->codigoMunicipio ?? null,
                    'codigoLocalidad' => $obj->datosGenerales->localizacion->codigoLocalidad ?? null,
                    'nombreProvincia' => $obj->datosGenerales->localizacion->nombreProvincia ?? null,
                    'nombreMunicipio' => $obj->datosGenerales->localizacion->nombreMunicipio ?? null,
                    'nombreLocalidad' => $obj->datosGenerales->localizacion->nombreLocalidad ?? null,

                    // Datos generales / georeferenciación
                    'gmLongitud' => $obj->datosGenerales->georeferenciacion->gmLongitud ?? null,
                    'gmLatitud' => $obj->datosGenerales->georeferenciacion->gmLatitud ?? null,

                    // Datos restauración
                    'subtipoRecurso' => $obj->datosRestauracion->subtipoRecurso ?? null,
                    'nombreSubtipoRecurso' => $obj->datosRestauracion->nombreSubtipoRecurso ?? null,
                    'capacidad' => $obj->datosRestauracion->capacidad ?? null,
                ]);

                // Creación de los servicios y asociación al restaurante
                if (isset($obj->servicios)) {
                    if (is_array($obj->servicios->servicio)) {
                        foreach ($obj->servicios->servicio as $service) {
                            $services[] = $service;
                        }
                    } else {
                        $services[] = (object)[
                            'codigo' => $obj->servicios->servicio->codigo,
                            'nombre' => $obj->servicios->servicio->nombre
                        ];
                    }
                    $uniqueServices = array_unique($services, SORT_REGULAR);
                    $services = [];
                    foreach ($uniqueServices as $uniqueService) {
                        $services[] = Service::updateOrCreate([
                            'codigo' => $uniqueService->codigo,
                            'nombre' => $uniqueService->nombre
                        ]);

                    }
                    $services = array_unique($services, SORT_REGULAR);

                    foreach ($obj->servicios->servicio as $service) {
                        $filteredArr = array_filter($services,
                            function ($obj) use (&$service) {
                                if (is_array($service)) {
                                    foreach ($service as $s) {
                                        return $obj->nombre === $s->nombre;
                                    }
                                }
                                $s = (object)[
                                    'codigo' => $service->codigo ?? null,
                                    'nombre' => $service->nombre ?? null
                                ];
                                return $obj->nombre === $s->nombre;

                            });
                        if ($filteredArr) {
                            foreach ($filteredArr as $filteredService) {
                                $restaurant->services()->syncWithoutDetaching($filteredService->id);
                            }
                        }
                    }
                }

                // Creación de las imágenes y asociación al restaurante
                if (isset($obj->galeria)) {
                    if (is_array($obj->galeria)) {
                        foreach ($obj->galeria as $image) {
                            if (isset($image->src)) {
                                Image::updateOrCreate([
                                    'src' => is_object($image->src) ? null : $image->src,
                                    'titulo' => is_object($image->titulo) ? null : $image->titulo ?? null,
                                    'imageable_id' => $restaurant->id,
                                    'imageable_type' => 'restaurant'
                                ]);
                            }
                        }
                    } else {
                        if (isset($obj->galeria->src)) {
                            Image::updateOrCreate([
                                'src' => is_object($obj->galeria->src) ? null : $obj->galeria->src,
                                'titulo' => is_object($obj->galeria->titulo) ? null : $obj->galeria->titulo ?? null,
                                'imageable_id' => $restaurant->id,
                                'imageable_type' => 'restaurant'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
