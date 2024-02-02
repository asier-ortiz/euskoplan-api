<?php

namespace Database\Seeders;

use App\Models\Fair;
use App\Models\Image;
use Illuminate\Database\Seeder;

class FairSeeder extends Seeder
{

    public function run($language)
    {
        $path = database_path() . '/data/' . $language . '-fairs.json';
        $json = file_get_contents($path);
        $jsonData = json_decode($json, false);

        foreach ($jsonData as $obj) {

            if (isset($obj->fechaActualizacion)) {

                // Creación de los parques temáticos
                $fair = Fair::updateOrCreate([

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
                    'nombreProvincia' => $obj->datosGenerales->localizacion->nombreProvincia ?? null,
                    'nombreMunicipio' => $obj->datosGenerales->localizacion->nombreMunicipio ?? null,

                    // Datos generales / georeferenciación
                    'gmLongitud' => $obj->datosGenerales->georeferenciacion->gmLongitud ?? null,
                    'gmLatitud' => $obj->datosGenerales->georeferenciacion->gmLatitud ?? null,

                    // Datos parques temáticos
                    'atracciones' => $obj->datosParquesTematicos->atracciones ?? null,
                    'horario' => $obj->datosParquesTematicos->horario ?? null,
                    'tarifas' => $obj->datosParquesTematicos->tarifas ?? null

                ]);

                // Creación de las imágenes y asociación al parque temático
                if (isset($obj->galeria)) {
                    if (is_array($obj->galeria)) {
                        foreach ($obj->galeria as $image) {
                            if (isset($image->src)) {
                                Image::updateOrCreate([
                                    'src' => is_object($image->src) ? null : $image->src,
                                    'titulo' => is_object($image->titulo) ? null : $image->titulo ?? null,
                                    'imageable_id' => $fair->id,
                                    'imageable_type' => 'fair'
                                ]);
                            }
                        }
                    } else {
                        if (isset($obj->galeria->src)) {
                            Image::updateOrCreate([
                                'src' => is_object($obj->galeria->src) ? null : $obj->galeria->src,
                                'titulo' => is_object($obj->galeria->titulo) ? null : $obj->galeria->titulo ?? null,
                                'imageable_id' => $fair->id,
                                'imageable_type' => 'fair'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
