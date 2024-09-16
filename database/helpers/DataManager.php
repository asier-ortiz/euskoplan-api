<?php

namespace Database\Helpers;

use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Log;
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

        if (empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            Log::warning("Invalid or empty URL provided to getXML. Skipping this resource.");
            return null;
        }

        $maxSize = 500 * 1024 * 1024; // 500MB

        $headers = get_headers($url, 1);
        if (isset($headers['Content-Length']) && $headers['Content-Length'] > $maxSize) {
            Log::error("The file at $url exceeds the maximum allowed size of 500MB.");
            return null;
        }

        if (file_exists(storage_path() . '/app/downloads/temp')) {
            self::removeDirectory(storage_path() . '/app/downloads/temp');
        }

        mkdir(storage_path() . '/app/downloads/temp', 0775, true);

        $data = null;
        $images = null;

        try {
            $zipDestinationPath = storage_path("app/downloads/temp/") . uniqid(time(), true) . ".zip";
            if (!@file_put_contents($zipDestinationPath, fopen($url, 'r'))) {
                Log::warning("Failed to download file from $url.");
                return null;
            }

            if (filesize($zipDestinationPath) > $maxSize) {
                unlink($zipDestinationPath);
                Log::error("The downloaded file at $url exceeds the maximum allowed size of 500MB.");
                return null;
            }

            $zip = new ZipArchive();
            if ($zip->open($zipDestinationPath, ZipArchive::RDONLY) === true) {
                $unzipDestinationPath = storage_path("app/downloads/unzip/");
                if (!file_exists($unzipDestinationPath)) mkdir($unzipDestinationPath, 0775, true);
                $zip->extractTo($unzipDestinationPath);
                $zip->close();
            } else {
                Log::warning("Failed to open ZIP file at $zipDestinationPath.");
                return null;
            }

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
            Log::error($e->getMessage());
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
                Log::warning("The resource type '$resourceType' is invalid.");
                return;
            }
            if (!in_array($language, self::LANGUAGES)) {
                Log::warning("The resource language '$language' is invalid.");
                return;
            }

            $url = self::RESOURCES[$resourceType];
            $json = @file_get_contents($url);

            if ($json === false) {
                Log::warning("Failed to fetch resource from '$url'.");
                return;
            }

            $decodedJson = json_decode($json);

            if (!$decodedJson) {
                Log::warning("Could not decode the requested resource from '$url'.");
                return;
            }

            if (!file_exists(database_path() . '/data')) {
                mkdir(database_path() . '/data', 0775, true);
            }

            $data = [];
            foreach ($decodedJson as $jsonObject) {
                $zipFileUrl = $jsonObject->zipFile ?? null;
                if ($zipFileUrl !== null && !empty($zipFileUrl)) {
//                    Log::info("zipFileUrl for resource: $resourceType in language: $language - $zipFileUrl");

                    // Validate the URL format
                    if (filter_var($zipFileUrl, FILTER_VALIDATE_URL) === false) {
                        Log::warning("Invalid URL format: $zipFileUrl. Skipping to next item.");
                        continue;
                    }

                    $dataResource = $this->getXML($zipFileUrl, $language);
                    if ($dataResource !== null) {
                        $data[] = $dataResource;
                    } else {
                        Log::warning("Failed to get XML data for zip file URL: $zipFileUrl.");
                    }
                } else {
                    Log::warning("Invalid or empty zip file URL found for a resource item. Skipping to next item.");
                }
            }

            if (!empty($data)) {
                $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                file_put_contents(database_path() . '/data/' . $language . '-' . $resourceType . '.json', $json_data);
            } else {
                Log::warning("No data was fetched for resource type '$resourceType' in language '$language'.");
            }
        } catch (Exception $e) {
            Log::error("Error while fetching resource for '$resourceType' in language '$language': " . $e->getMessage());
        }
    }

}
