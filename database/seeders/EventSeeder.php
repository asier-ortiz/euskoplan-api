<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Image;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{

    public function run($language)
    {
        $path = database_path() . '/data/' . $language . '-events.json';
        $json = file_get_contents($path);
        $jsonData = json_decode($json, false);

        foreach ($jsonData as $obj) {

            if (isset($obj->fechaActualizacion)) {

                // Cambio formato fecha de inicio
                $fechaInicio = null;
                if (isset($obj->datosAgenda->fechaInicio)) {
                    try {
                        $fechaInicio = Carbon::createFromFormat('d/m/Y', trim($obj->datosAgenda->fechaInicio))->format('Y-m-d');
                    } catch (InvalidFormatException $e) {
                        $fechaInicio = null;
                    }
                }

                // Cambio formato fecha de inicio
                $fechaFin = null;
                if (isset($obj->datosAgenda->fechaFin)) {
                    try {
                        $fechaFin = Carbon::createFromFormat('d/m/Y', trim($obj->datosAgenda->fechaFin))->format('Y-m-d');
                    } catch (InvalidFormatException $e) {
                        $fechaFin = null;
                    }
                }

                // Creación de los eventos
                $event = Event::updateOrCreate([

                    'fechaActualizacion' => $obj->fechaActualizacion,
                    'idioma' => $language,

                    // Datos generales
                    'codigo' => $obj->datosGenerales->codigo ?? null,
                    'tipoRecurso' => $obj->datosGenerales->tipoRecurso ?? null,
                    'nombre' => $obj->datosGenerales->nombre ?? null,
                    'descripcion' => $obj->datosGenerales->descripcion ?? null,
                    'urlFichaPortal' => $obj->datosGenerales->urlFichaPortal ?? null,

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

                    // Datos agenda
                    'subTipoRecurso' => $obj->datosAgenda->subTipoRecurso ?? null,
                    'nombreSubTipoRecurso' => $obj->datosAgenda->nombreSubTipoRecurso ?? null,
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin
                ]);

                // Creación de las imágenes y asociación al evento
                if (isset($obj->galeria)) {
                    if (is_array($obj->galeria)) {
                        foreach ($obj->galeria as $image) {
                            if (isset($image->src)) {
                                Image::updateOrCreate([
                                    'src' => is_object($image->src) ? null : $image->src,
                                    'titulo' => is_object($image->titulo) ? null : $image->titulo ?? null,
                                    'imageable_id' => $event->id,
                                    'imageable_type' => 'event'
                                ]);
                            }
                        }
                    } else {
                        if (isset($obj->galeria->src)) {
                            Image::updateOrCreate([
                                'src' => is_object($obj->galeria->src) ? null : $obj->galeria->src,
                                'titulo' => is_object($obj->galeria->titulo) ? null : $obj->galeria->titulo ?? null,
                                'imageable_id' => $event->id,
                                'imageable_type' => 'event'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
