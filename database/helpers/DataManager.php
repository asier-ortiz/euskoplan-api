<?php

namespace Database\Helpers;

use DOMDocument;
use DOMXPath;
use Exception;
use SimpleXMLElement;
use ZipArchive;

class DataManager
{

    const RESOURCES = [
        'accommodations' => 'https://www.opendata.euskadi.eus/contenidos/ds_recursos_turisticos/alojamiento_de_euskadi/opendata/alojamientos.json',
        'caves' => 'https://www.opendata.euskadi.eus/contenidos/ds_recursos_turisticos/cuevas_restos_arqueologicos/opendata/cuevas.json',
        'culturals' => 'https://www.euskadi.eus/contenidos/ds_recursos_turisticos/edificios_religiosos_castillos/opendata/edificios.json',
        'events' => 'https://www.opendata.euskadi.eus/contenidos/ds_eventos/eventos_turisticos/opendata/agenda.json',
        'fairs' => 'https://www.opendata.euskadi.eus/contenidos/ds_recursos_turisticos/parques_de_atracciones_euskadi/opendata/parques.json',
        'museums' => 'https://www.opendata.euskadi.eus/contenidos/ds_recursos_turisticos/museos_centros_interpretacion/opendata/museos.json',
        'naturals' => 'https://www.opendata.euskadi.eus/contenidos/ds_recursos_turisticos/espacios_naturales_euskadi/opendata/espacios-naturales.json',
        'restaurants' => 'https://www.opendata.euskadi.eus/contenidos/ds_recursos_turisticos/restaurantes_sidrerias_bodegas/opendata/restaurantes.json',
        'localities' => 'https://www.opendata.euskadi.eus/contenidos/ds_recursos_turisticos/pueblos_euskadi_turismo/opendata/pueblos.json'
    ];

    const LANGUAGES = ['es', 'eu', 'fr', 'de', 'en'];

    private function getXML($url, $language): SimpleXMLElement|bool|string|null
    {

        if (file_exists(storage_path() . '/app/downloads/temp')) {
            self::removeDirectory(storage_path() . '/app/downloads/temp');
        }

        mkdir(storage_path() . '/app/downloads/temp', 0775, true);

        $data = null;
        $images = null;

        try {
            $zipDestinationPath = storage_path("app/downloads/temp/") . uniqid(time(), true) . ".zip";
            if (!@file_put_contents($zipDestinationPath, fopen($url, 'r'))) return null;
            $zip = new ZipArchive();
            $zip->open($zipDestinationPath, ZIPARCHIVE::RDONLY);
            $unzipDestinationPath = storage_path("app/downloads/unzip/");
            if (!file_exists($unzipDestinationPath)) mkdir($unzipDestinationPath, 0775, true);
            $zip->extractTo($unzipDestinationPath);
            $zip->close();
            if (!isset(scandir($unzipDestinationPath)[2])) return null;
            $uncompressedFolderName = array_diff(scandir($unzipDestinationPath), array('..', '.'))[2];
            $folderCode = ltrim(explode('_', $uncompressedFolderName)[0], '0');
            $dataFolderPath = $unzipDestinationPath . $uncompressedFolderName . '/' . $language . '_' . $folderCode . '/data/';

            if (file_exists($dataFolderPath)) {
                if (isset(scandir($dataFolderPath)[2])) {
                    $dataFilePath = $dataFolderPath . scandir($dataFolderPath)[2];
                    if (file_exists($dataFilePath)) {
                        $data = file_get_contents($dataFilePath);

                        // Galería imágenes
                        if (isset(scandir($dataFolderPath)[3]) && $data) {
                            $galleryFilePath = $dataFolderPath . scandir($dataFolderPath)[3];
                            if (file_exists($galleryFilePath)) {
                                $galleryData = file_get_contents($galleryFilePath);
                                $document = new DOMDocument;
                                $internalErrors = libxml_use_internal_errors(true);
                                libxml_use_internal_errors($internalErrors);
                                if ($document->loadXML($galleryData)) {
                                    $xpath = new DOMXPath($document);
                                    $galleryNodes = $xpath->query('//item[@name="multimedia"]//item[@name="imagenes"]//item[@name="galeria"]/*/item');
                                    foreach ($galleryNodes as $galleryNode) {
                                        $src = $xpath->query('.//item[@name="archivoMultimedia"]', $galleryNode)->item(0)->nodeValue;
                                        $title = $xpath->query('.//item[@name="tituloMultimedia"]', $galleryNode)->item(0)->nodeValue;
                                        $images[] = ['src' => $src, 'title' => $title];
                                    }
                                    if ($images && $document->loadXML($data)) {
                                        $root = $document->documentElement;
                                        foreach ($images as $image) {
                                            $gallery = $document->createElement('galeria');
                                            $gallery->appendChild($document->createElement('src', 'https://www.opendata.euskadi.eus' . htmlspecialchars($image['src'])));
                                            $gallery->appendChild($document->createElement('titulo', htmlspecialchars($image['title'])));
                                            $root->appendChild($gallery);
                                        }
                                        $data = $document->saveXML();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            print $e . PHP_EOL;
        } finally {
            self::removeDirectory(storage_path("app/downloads"));
            if ($data) $data = simplexml_load_string($data, null, LIBXML_NOCDATA);
            return $data;
        }
    }

    private function removeDirectory($dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") self::removeDirectory($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }

    public function fetchResource($resourceType, $language): void
    {
        try {
            if (!array_key_exists($resourceType, self::RESOURCES)) {
                throw new Exception('Requested resource type is invalid.');
            }
            if (!in_array($language, self::LANGUAGES)) {
                throw new Exception('Requested resource language is invalid.');
            }

            $url = self::RESOURCES[$resourceType];
            $json = file_get_contents($url);
            $decodedJson = json_decode($json);

            if (!$decodedJson) {
                throw new Exception('Could not decode requested resource.');
            }

            if (!file_exists(database_path() . '/data')) {
                mkdir(database_path() . '/data', 0775, true);
            }

            $data = [];
            foreach ($decodedJson as $jsonObject) {
                $zipFileUrl = $jsonObject->zipFile ?? null;
                if ($zipFileUrl !== null) {
                    $dataResource = $this->getXML($zipFileUrl, $language);
                    if ($dataResource !== null) {
                        $data[] = $dataResource;
                    }
                }
            }

            if (!empty($data)) {
                $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                file_put_contents(database_path() . '/data/' . $language . '-' . $resourceType . '.json', $json_data);
            }
        } catch (Exception $e) {
            echo match ($e->getMessage()) {
                'Requested resource type is invalid.' => "Error: The resource type '$resourceType' is invalid." . PHP_EOL,
                'Requested resource language is invalid.' => "Error: The resource language '$language' is invalid." . PHP_EOL,
                'Could not decode requested resource.' => "Error: Unable to decode the resource at '$url'." . PHP_EOL,
                default => "Error: An unexpected error occurred - " . $e->getMessage() . PHP_EOL,
            };
        }
    }
}
