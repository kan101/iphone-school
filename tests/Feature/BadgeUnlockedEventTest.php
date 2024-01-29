<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Events\BadgeUnlocked;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Achievement;

class BadgeUnlockedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fires_badge_unlocked_event_when_zero_achievements_unlocked()
    {
        $this->create_badge_unlocked_event_test(0, 'Beginner');
    }

    /** @test */
    public function it_fires_badge_unlocked_event_when_four_achievements_unlocked()
    {
        $this->create_badge_unlocked_event_test(4, 'Intermediate');
    }

    /** @test */
    public function it_fires_badge_unlocked_event_when_eight_achievements_unlocked()
    {
        $this->create_badge_unlocked_event_test(8, 'Advanced');
    }

    /** @test */
    public function it_fires_badge_unlocked_event_when_ten_achievements_unlocked()
    {
        $this->create_badge_unlocked_event_test(10, 'Master');
    }

    public function create_badge_unlocked_event_test(int $milestone, string $badge_name)
    {
        Event::fake();
        
        $user = User::factory()->create();

        Achievement::factory()->count($milestone)->create(['user_id' => $user->id]);

        $user->checkAndUnlockBadge();

        /** Assert that the BadgeUnlocked event was fired with the expected badge */
        Event::assertDispatched(BadgeUnlocked::class, function ($e) use ($user, $badge_name) {
            return $e->user->id === $user->id && $e->badge_name === $badge_name;
        });
    }
}
