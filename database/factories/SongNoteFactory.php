<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\SongNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SongNote> */
class SongNoteFactory extends Factory
{
    /** @inheritdoc */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'song_id' => Song::factory(),
            'content' => fake()->paragraph(),
        ];
    }
}
