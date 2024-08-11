<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $accommodation_seeder = new AccommodationSeeder();
        $accommodation_seeder->run('es');

        $cave_seeder = new CaveSeeder();
        $cave_seeder->run('es');

        $cultural_seeder = new CulturalSeeder();
        $cultural_seeder->run('es');

        $event_seeder = new EventSeeder();
        $event_seeder->run('es');

        $fair_seeder = new FairSeeder();
        $fair_seeder->run('es');

        $museum_seeder = new MuseumSeeder();
        $museum_seeder->run('es');

        $natural_seeder = new NaturalSeeder();
        $natural_seeder->run('es');

        $restaurant_seeder = new RestaurantSeeder();
        $restaurant_seeder->run('es');

        $locality_seeder = new LocalitySeeder();
        $locality_seeder->run('es');

        $user_seeder = new UserSeeder();
        $user_seeder->run();

        $plan_seeder = new PlanSeeder();
        $plan_seeder->run();
    }
}
