<?php

namespace App\Console\Commands;

use Database\Helpers\DataManager;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CollectionsCron extends Command
{
    protected $signature = 'collections:cron';
    protected $description = 'Updates database collections';

    public function handle(): int
    {
        info("Starting database collections update Cron Job at " . now());

        // Lista de recursos a descargar
        $resources = [
            'accommodations', 'caves', 'culturals', 'events',
            'fairs', 'museums', 'naturals', 'restaurants', 'localities'
        ];

        // Idiomas a procesar
        $languages = ['es', 'en'];

        // Instancia de DataManager
        $dataManager = new DataManager();

        foreach ($resources as $resource) {
            foreach ($languages as $language) {
                try {
                    // Descargar el recurso y actualizar la base de datos directamente
                    $this->updateResource($dataManager, $resource, $language);
                    Log::info("Updated resource: $resource in language: $language");
                } catch (Exception $e) {
                    Log::error("Failed to update resource: $resource in language: $language. Error: " . $e->getMessage());
                }
            }
        }

        info("Finishing database collections update Cron Job at " . now());

        return CommandAlias::SUCCESS;
    }

    protected function updateResource(DataManager $dataManager, $resource, $language)
    {
        // Descargar y procesar el recurso
        $dataManager->fetchResource($resource, $language);

        // Llamar al seeder correspondiente
        $this->runSeeder($resource, $language);
    }

    protected function runSeeder($resource, $language)
    {
        switch ($resource) {
            case 'accommodations':
                Artisan::call('db:seed', ['--class' => 'AccommodationSeeder', '--language' => $language]);
                break;
            case 'caves':
                Artisan::call('db:seed', ['--class' => 'CaveSeeder', '--language' => $language]);
                break;
            case 'culturals':
                Artisan::call('db:seed', ['--class' => 'CulturalSeeder', '--language' => $language]);
                break;
            case 'events':
                Artisan::call('db:seed', ['--class' => 'EventSeeder', '--language' => $language]);
                break;
            case 'fairs':
                Artisan::call('db:seed', ['--class' => 'FairSeeder', '--language' => $language]);
                break;
            case 'museums':
                Artisan::call('db:seed', ['--class' => 'MuseumSeeder', '--language' => $language]);
                break;
            case 'naturals':
                Artisan::call('db:seed', ['--class' => 'NaturalSeeder', '--language' => $language]);
                break;
            case 'restaurants':
                Artisan::call('db:seed', ['--class' => 'RestaurantSeeder', '--language' => $language]);
                break;
            case 'localities':
                Artisan::call('db:seed', ['--class' => 'LocalitySeeder', '--language' => $language]);
                break;
            default:
                Log::warning("No seeder found for resource: $resource");
                break;
        }
    }
}
