<?php

namespace Database\Seeders;

use App\Models\Cultural;
use App\Models\Image;
use App\Models\Service;
use Illuminate\Database\Seeder;

class CulturalSeeder extends Seeder
{

    public function run($language)
    {
        $path = database_path() . '/data/' . $language . '-culturals.json';
        $json = file_get_contents($path);
        $jsonData = json_decode($json, false);

        foreach ($jsonData as $obj) {

            if (isset($obj->fechaActualizacion)) {

                // Creación de los recursos culturales
                $cultural = Cultural::updateOrCreate([

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

                    // Datos patrimonio cultural
                    'subTipoRecurso' => $obj->datosPatrimonioCultural->subTipoRecurso ?? null,
                    'nombreSubTipoRecurso' => $obj->datosPatrimonioCultural->nombreSubTipoRecurso ?? null,
                    'tipoMonumento' => $obj->datosPatrimonioCultural->tipoMonumento ?? null,
                    'estiloArtistico' => $obj->datosPatrimonioCultural->estiloArtistico ?? null,

                ]);

                // Creación de los servicios y asociación al recurso cultural
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
                                $cultural->services()->syncWithoutDetaching($filteredService->id);
                            }
                        }
                    }
                }

                // Creación de las imágenes y asociación al recurso cultural
                if (isset($obj->galeria)) {
                    if (is_array($obj->galeria)) {
                        foreach ($obj->galeria as $image) {
                            if (isset($image->src)) {
                                Image::updateOrCreate([
                                    'src' => is_object($image->src) ? null : $image->src,
                                    'titulo' => is_object($image->titulo) ? null : $image->titulo ?? null,
                                    'imageable_id' => $cultural->id,
                                    'imageable_type' => 'cultural'
                                ]);
                            }
                        }
                    } else {
                        if (isset($obj->galeria->src)) {
                            Image::updateOrCreate([
                                'src' => is_object($obj->galeria->src) ? null : $obj->galeria->src,
                                'titulo' => is_object($obj->galeria->titulo) ? null : $obj->galeria->titulo ?? null,
                                'imageable_id' => $cultural->id,
                                'imageable_type' => 'cultural'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
