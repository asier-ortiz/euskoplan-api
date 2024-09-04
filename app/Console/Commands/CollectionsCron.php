<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCollection;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CollectionsCron extends Command
{
    protected $signature = 'collections:cron';
    protected $description = 'Downloads and updates database collections in multiple languages by dispatching jobs to the queue';

    public function handle(): int
    {
        Log::info("Starting database collections Cron at " . now());

        // Lista de recursos a descargar
        $resources = [
            'accommodations', 'caves', 'culturals', 'events',
            'fairs', 'museums', 'naturals', 'restaurants', 'localities'
        ];

        // Idiomas a procesar
        $languages = ['es', 'en'];

        $totalJobs = count($resources) * count($languages);
        Log::info("Total jobs to dispatch: $totalJobs");

        foreach ($resources as $resource) {
            foreach ($languages as $language) {
                try {
                    Log::info("Preparing to dispatch job for resource: $resource in language: $language");

                    // Despachar el job genérico para la colección y el idioma
                    ProcessCollection::dispatch($resource, $language);

                    Log::info("Successfully dispatched job for resource: $resource in language: $language");
                } catch (Exception $e) {
                    Log::error("Failed to dispatch job for resource: $resource in language: $language. Error: " . $e->getMessage(), [
                        'resource' => $resource,
                        'language' => $language,
                        'exception' => $e
                    ]);
                }
            }
        }

        Log::info("All jobs dispatched successfully.");
        Log::info("Finishing database collections Cron at " . now());

        return CommandAlias::SUCCESS;
    }
}
