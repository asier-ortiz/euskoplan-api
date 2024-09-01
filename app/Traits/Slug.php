<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Slug
{
    /**
     * Generate the slug for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the value of the model's route key.
     *
     * @return string
     */
    public function getRouteKey(): string
    {
        // Combine title and id to generate slug
        return Str::slug("$this->nombre-$this->codigo");
    }

    /**
     * Resolve the model by extracting its ID from the slug.
     *
     * @param string $value
     * @param string|null $field
     * @return mixed
     */
    public function resolveRouteBinding($value, $field = null): mixed
    {
        // Extract the ID from the slug
        $id = last(explode('-', $value));

        // Use the parent method to resolve the binding
        return parent::resolveRouteBinding($id, $field);
    }
}
