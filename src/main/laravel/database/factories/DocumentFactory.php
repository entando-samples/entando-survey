<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    public function definition()
    {
        return [
            "title" => $this->faker->sentence(),
            "body" => "<p>" . implode("</p><p>", $this->faker->paragraphs(2)) . "</p>",
            "youtube_url" => "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
            'cover_image' => makeTestFile(),
            'file' => makeTestFile('pdf'),
            'images' => [makeTestFile(), makeTestFile()],
            "created_by" => User::inRandomOrder()->first()->id,
            "updated_by" => User::inRandomOrder()->first()->id,
        ];
    }
}
