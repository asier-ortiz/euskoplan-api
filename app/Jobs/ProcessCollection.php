<?php

namespace App\Jobs;

use Database\Helpers\DataManager;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessCollection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $collection;
    protected $language;

    public $timeout = 3600; // Timeout de 1 hora
    public $tries = 3; // Intentos antes de fallar

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($collection, $language)
    {
        $this->collection = $collection;
        $this->language = $language;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("Starting to process $this->collection in language: $this->language at " . now());

            // Instanciar DataManager para descargar los datos
            $dataManager = new DataManager();
            Log::info("Downloading resource for $this->collection in language: $this->language...");
            $dataManager->fetchResource($this->collection, $this->language);
            Log::info("Successfully downloaded resource for $this->collection in language: $this->language.");

            // Establecer el idioma en la configuración
            config(['app.seeder_language' => $this->language]);

            // Procesar el seeder correspondiente según la colección
            Log::info("Starting database seeding for $this->collection in language: $this->language.");
            $this->runSeeder($this->collection);
            Log::info("Successfully completed database seeding for $this->collection in language: $this->language at " . now());
        } catch (Exception $e) {
            Log::error("Error processing $this->collection in language: $this->language. Error: " . $e->getMessage());
        }
    }

    protected function runSeeder($collection)
    {
        switch ($collection) {
            case 'accommodations':
                Artisan::call('db:seed', ['--class' => 'AccommodationSeeder']);
                break;
            case 'caves':
                Artisan::call('db:seed', ['--class' => 'CaveSeeder']);
                break;
            case 'culturals':
                Artisan::call('db:seed', ['--class' => 'CulturalSeeder']);
                break;
            case 'events':
                Artisan::call('db:seed', ['--class' => 'EventSeeder']);
                break;
            case 'fairs':
                Artisan::call('db:seed', ['--class' => 'FairSeeder']);
                break;
            case 'museums':
                Artisan::call('db:seed', ['--class' => 'MuseumSeeder']);
                break;
            case 'naturals':
                Artisan::call('db:seed', ['--class' => 'NaturalSeeder']);
                break;
            case 'restaurants':
                Artisan::call('db:seed', ['--class' => 'RestaurantSeeder']);
                break;
            case 'localities':
                Artisan::call('db:seed', ['--class' => 'LocalitySeeder']);
                break;
            default:
                Log::warning("No seeder found for collection: $collection");
                break;
        }
    }
}
