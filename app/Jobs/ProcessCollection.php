<?php

namespace App\Jobs;

use Database\Helpers\DataManager;
use Database\Seeders\AccommodationSeeder;
use Database\Seeders\CaveSeeder;
use Database\Seeders\CulturalSeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\FairSeeder;
use Database\Seeders\LocalitySeeder;
use Database\Seeders\MuseumSeeder;
use Database\Seeders\NaturalSeeder;
use Database\Seeders\RestaurantSeeder;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCollection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $collection;
    protected $language;

//    public $queue = 'collections';
    public $timeout = 3600; // Timeout de 1 hora
    public $tries = 1; // Intentos antes de fallar

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
    public function handle(): void
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

    protected function runSeeder($collection): void
    {
        switch ($collection) {
            case 'accommodations':
                (new AccommodationSeeder())->run();
                break;
            case 'caves':
                (new CaveSeeder())->run();
                break;
            case 'culturals':
                (new CulturalSeeder())->run();
                break;
            case 'events':
                (new EventSeeder())->run();
                break;
            case 'fairs':
                (new FairSeeder())->run();
                break;
            case 'museums':
                (new MuseumSeeder())->run();
                break;
            case 'naturals':
                (new NaturalSeeder())->run();
                break;
            case 'restaurants':
                (new RestaurantSeeder())->run();
                break;
            case 'localities':
                (new LocalitySeeder())->run();
                break;
            default:
                Log::warning("No seeder found for collection: $collection");
                break;
        }
    }
}
