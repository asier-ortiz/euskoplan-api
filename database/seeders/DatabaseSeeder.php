<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Idiomas a procesar
        $languages = ['es', 'en'];

        // Lista de seeders para procesar
        $seeders = [
            AccommodationSeeder::class,
            CaveSeeder::class,
            CulturalSeeder::class,
            EventSeeder::class,
            FairSeeder::class,
            LocalitySeeder::class,
            MuseumSeeder::class,
            NaturalSeeder::class,
            RestaurantSeeder::class,
        ];

        // Para cada idioma, ejecutar todos los seeders
        foreach ($languages as $language) {
            // Establece el idioma en la configuración para que los seeders puedan usarlo
            config(['app.seeder_language' => $language]);

            foreach ($seeders as $seederClass) {
                $this->call($seederClass);
            }
        }

        // Seeders adicionales
        $this->call(UserSeeder::class);
        $this->call(PlanSeeder::class);
    }
}
