<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use App\Models\Achievement;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function create_achievement(User $user, string $achievement_key, string $achievement_type, int $current_milestone, string $achievement_name): Achievement
    {
        return Achievement::factory()->create([
            'user_id' => $user->id,
            'achievement_key' => $achievement_key,
            'achievement_type' => $achievement_type,
            'current_milestone' => $current_milestone,
            'achievement_name' => $achievement_name,
        ]);
    }
}
