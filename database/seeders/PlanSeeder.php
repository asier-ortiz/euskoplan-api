<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Step;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Plan::factory(10)->create()->each(function ($plan) {
            foreach (range(0, 4) as $indice) {
                Step::factory()->create([
                    'plan_id' => $plan->id,
                    'indice' => $indice
                ]);
            }
        });
    }
}
