<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(5, true),
            "description" => "<p>" . implode("</p><p>", $this->faker->paragraphs(2)) . "</p>",
        ];
    }
}
