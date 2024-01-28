<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $achievementsConfig = config('achievements');

        $type = $this->faker->randomElement(['lessons_watched', 'comments_written']);
        $achievements = $achievementsConfig[$type];

        $achievementKey = $this->faker->randomElement(array_keys($achievements));
        $achievement = $achievements[$achievementKey];

        return [
            'user_id' => User::factory(),
            'achievement_key' => $achievementKey,
            'achievement_type' => $type,
            'achievement_name' => $achievement['name'],
            'current_milestone' => $achievement['milestone'],
        ];
    }
}
