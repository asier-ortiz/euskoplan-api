<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $accommodation_seeder = new AccommodationSeeder();
        $accommodation_seeder->run('es');
        $accommodation_seeder->run('eu');

        $cave_seeder = new CaveSeeder();
        $cave_seeder->run('es');
        $cave_seeder->run('eu');

        $cultural_seeder = new CulturalSeeder();
        $cultural_seeder->run('es');
        $cultural_seeder->run('eu');

        $event_seeder = new EventSeeder();
        $event_seeder->run('es');
        $event_seeder->run('eu');

        $fair_seeder = new FairSeeder();
        $fair_seeder->run('es');
        $fair_seeder->run('eu');

        $museum_seeder = new MuseumSeeder();
        $museum_seeder->run('es');
        $museum_seeder->run('eu');

        $natural_seeder = new NaturalSeeder();
        $natural_seeder->run('es');
        $natural_seeder->run('eu');

        $restaurant_seeder = new RestaurantSeeder();
        $restaurant_seeder->run('es');
        $restaurant_seeder->run('eu');

        $locality_seeder = new LocalitySeeder();
        $locality_seeder->run('es');
        $locality_seeder->run('eu');

        $user_seeder = new UserSeeder();
        $user_seeder->run();
    }
}
