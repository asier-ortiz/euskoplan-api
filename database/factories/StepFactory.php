<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Step;
use App\Models\Accommodation;
use App\Models\Cave;
use App\Models\Cultural;
use App\Models\Event;
use App\Models\Fair;
use App\Models\Museum;
use App\Models\Natural;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Step>
 */
class StepFactory extends Factory
{
    protected $model = Step::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resourceTypes = [
            'accommodation' => Accommodation::class,
            'cave' => Cave::class,
            'cultural' => Cultural::class,
            'event' => Event::class,
            'fair' => Fair::class,
            'museum' => Museum::class,
            'natural' => Natural::class,
            'restaurant' => Restaurant::class
        ];

        $selectedResource = $this->faker->randomElement(array_keys($resourceTypes));

        $resourceModel = $resourceTypes[$selectedResource];
        $resourceId = $resourceModel::inRandomOrder()->value('id');

        if (!$resourceId) {
            $resourceId = $resourceModel::factory()->create()->id;
        }

        return [
            'indicaciones' => $this->faker->sentence,
            'plan_id' => Plan::factory(),
            'planables_type' => $selectedResource,
            'planables_id' => $resourceId
        ];
    }
}
