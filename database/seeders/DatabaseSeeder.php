<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $accommodation_seeder = new AccommodationSeeder();
        $accommodation_seeder->run('es');
        $accommodation_seeder->run('en');

        $cave_seeder = new CaveSeeder();
        $cave_seeder->run('es');
        $cave_seeder->run('en');

        $cultural_seeder = new CulturalSeeder();
        $cultural_seeder->run('es');
        $cultural_seeder->run('en');

        $event_seeder = new EventSeeder();
        $event_seeder->run('es');
        $event_seeder->run('en');

        $fair_seeder = new FairSeeder();
        $fair_seeder->run('es');
        $fair_seeder->run('en');

        $locality_seeder = new LocalitySeeder();
        $locality_seeder->run('es');
        $locality_seeder->run('en');

        $museum_seeder = new MuseumSeeder();
        $museum_seeder->run('es');
        $museum_seeder->run('en');

        $natural_seeder = new NaturalSeeder();
        $natural_seeder->run('es');
        $natural_seeder->run('en');

        $restaurant_seeder = new RestaurantSeeder();
        $restaurant_seeder->run('es');
        $restaurant_seeder->run('en');

        $user_seeder = new UserSeeder();
        $user_seeder->run();

        $plan_seeder = new PlanSeeder();
        $plan_seeder->run();
    }
}
