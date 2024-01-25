<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Listeners\LessonWatchedListener;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LessonWatchedListenerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_achievement_on_first_lesson_watched()
    {
        Event::fake();

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $event = new LessonWatched($lesson, $user);
        $listener = new LessonWatchedListener();
        $listener->handle($event);

        $this->assertDatabaseHas('achievements', [
            'user_id' => $user->id,
            'achievement_type' => 'lessons_watched',
        ]);

        Event::assertDispatched(AchievementUnlocked::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    }
}
