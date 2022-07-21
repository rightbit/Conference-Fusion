<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConferenceSession>
 */
class ConferenceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(85),
            'description' => $this->faker->text(255),
            'participant_notes' => $this->faker->text(255),
            'staff_notes' => $this->faker->text(255),
            'seed_questions' => $this->faker->text( 255),
            'type_id' => '1',
            'conference_id' => rand(1,3),
            'track_id' => rand(1,5),
            'session_status_id' => '2',
        ];
    }
}
