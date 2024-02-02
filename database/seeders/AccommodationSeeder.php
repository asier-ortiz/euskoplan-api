<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Image;
use App\Models\Price;
use App\Models\Service;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{

    public function run($language)
    {
        $path = database_path() . '/data/' . $language . '-accommodations.json';
        $json = file_get_contents($path);
        $jsonData = json_decode($json, false);

        foreach ($jsonData as $obj) {

            if (isset($obj->fechaActualizacion)) {

                // Creación de los alojamientos
                $accommodation = Accommodation::updateOrCreate([

                    'fechaActualizacion' => $obj->fechaActualizacion,
                    'idioma' => $language,

                    // Datos generales
                    'codigo' => $obj->datosGenerales->codigo,
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
                    'nombreProvincia' => $obj->datosGenerales->localizacion->nombreProvincia ?? null,
                    'nombreMunicipio' => $obj->datosGenerales->localizacion->nombreMunicipio ?? null,

                    // Datos generales / georeferenciación
                    'gmLongitud' => $obj->datosGenerales->georeferenciacion->gmLongitud ?? null,
                    'gmLatitud' => $obj->datosGenerales->georeferenciacion->gmLatitud ?? null,

                    // Datos alojamiento
                    'subtipoRecurso' => $obj->datosAlojamiento->subtipoRecurso ?? null,
                    'nombreSubtipoRecurso' => $obj->datosAlojamiento->nombreSubtipoRecurso ?? null,
                    'categoria' => $obj->datosAlojamiento->categoria ?? null,
                    'capacidad' => $obj->datosAlojamiento->capacidad ?? null,
                    'annoApertura' => $obj->datosAlojamiento->annoApertura ?? null,
                    'numHabIndividuales' => $obj->datosAlojamiento->habitaciones->numHabIndividuales ?? null,
                    'numHabDobles' => $obj->datosAlojamiento->habitaciones->numHabDobles ?? null,
                    'numHabSalon' => $obj->datosAlojamiento->habitaciones->numHabSalon ?? null,
                    'numHabHasta4Plazas' => $obj->datosAlojamiento->habitaciones->numHabHasta4Plazas ?? null,
                    'numHabMas4Plazas' => $obj->datosAlojamiento->habitaciones->numHabMas4Plazas ?? null
                ]);

                // Creación de los servicios y asociación al alojamiento
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
                                $accommodation->services()->syncWithoutDetaching($filteredService->id);
                            }
                        }
                    }
                }

                // Creación de las imágenes y asociación al alojamiento
                if (isset($obj->galeria)) {
                    if (is_array($obj->galeria)) {
                        foreach ($obj->galeria as $image) {
                            if (isset($image->src)) {
                                Image::updateOrCreate([
                                    'src' => is_object($image->src) ? null : $image->src,
                                    'titulo' => is_object($image->titulo) ? null : $image->titulo ?? null,
                                    'imageable_id' => $accommodation->id,
                                    'imageable_type' => 'accommodation'
                                ]);
                            }
                        }
                    } else {
                        if (isset($obj->galeria->src)) {
                            Image::updateOrCreate([
                                'src' => is_object($obj->galeria->src) ? null : $obj->galeria->src,
                                'titulo' => is_object($obj->galeria->titulo) ? null : $obj->galeria->titulo ?? null,
                                'imageable_id' => $accommodation->id,
                                'imageable_type' => 'accommodation'
                            ]);
                        }
                    }
                }

                // Creación de los precios y asociación al alojamiento
                if (isset($obj->datosAlojamiento->precios)) {
                    if (is_array($obj->datosAlojamiento->precios->precio)) {
                        foreach ($obj->datosAlojamiento->precios->precio as $price) {
                            Price::updateOrCreate([
                                'codigo' => is_object($price->codigo) ? null : $price->codigo ?? null,
                                'nombre' => is_object($price->nombre) ? null : $price->nombre ?? null,
                                'capacidad' => is_object($price->codigo) ? null : $price->capacidad ?? null,

                                'precioMinimo' =>
                                    (!isset($price->precioMinimo)) ? null :
                                        (is_object($price->precioMinimo) ? null :
                                            ($price->precioMinimo ? number_format(floatval($price->precioMinimo), 2) : null)),

                                'precioMaximo' =>
                                    (!isset($price->precioMaximo)) ? null :
                                        (is_object($price->precioMaximo) ? null :
                                            ($price->precioMaximo ? number_format(floatval($price->precioMaximo), 2) : null)),

                                'accommodation_id' => $accommodation->id
                            ]);
                        }
                    } else {
                        Price::updateOrCreate([
                            'codigo' => is_object($obj->datosAlojamiento->precios->precio->codigo) ? null : $obj->datosAlojamiento->precios->precio->codigo ?? null,
                            'nombre' => is_object($obj->datosAlojamiento->precios->precio->nombre) ? null : $obj->datosAlojamiento->precios->precio->nombre ?? null,
                            'capacidad' => is_object($obj->datosAlojamiento->precios->precio->capacidad) ? null : $obj->datosAlojamiento->precios->precio->capacidad ?? null,

                            'precioMinimo' =>
                                (!isset($obj->datosAlojamiento->precios->precio->precioMinimo)) ? null :
                                    (is_object($obj->datosAlojamiento->precios->precio->precioMinimo) ? null :
                                        ($obj->datosAlojamiento->precios->precio->precioMinimo ? number_format(floatval($obj->datosAlojamiento->precios->precio->precioMinimo), 2) : null)),

                            'precioMaximo' =>
                                (!isset($obj->datosAlojamiento->precios->precio->precioMaximo)) ? null :
                                    (is_object($obj->datosAlojamiento->precios->precio->precioMaximo) ? null :
                                        ($obj->datosAlojamiento->precios->precio->precioMaximo ? number_format(floatval($obj->datosAlojamiento->precios->precio->precioMaximo), 2) : null)),

                            'accommodation_id' => $accommodation->id
                        ]);
                    }
                }
            }
        }
    }
}
