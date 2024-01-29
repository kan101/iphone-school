<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Listeners\LessonWatchedListener;
use App\Models\Achievement;
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
        $user = User::factory()->create();
        $this->create_test_for_achievement('First Lesson Watched', 1, $user);
    }

    /** @test */
    public function it_creates_achievement_on_five_lessons_watched()
    {
        $user = User::factory()->create();

        $this->create_prior_achievement($user, 'first_lesson_watched', 1, 'First Lesson Watched');
        $this->create_test_for_achievement('5 Lessons Watched', 5, $user);
    }

    /** @test */
    public function it_creates_achievement_on_ten_lessons_watched()
    {
        $user = User::factory()->create();

        $this->create_prior_achievement($user, 'five_lessons_watched', 5, '5 Lessons Watched');
        $this->create_test_for_achievement('10 Lessons Watched', 10, $user);
    }

    /** @test */
    public function it_creates_achievement_on_twenty_five_lessons_watched()
    {
        $user = User::factory()->create();

        $this->create_prior_achievement($user, 'ten_lessons_watched', 10, '10 Lessons Watched');
        $this->create_test_for_achievement('25 Lessons Watched', 25, $user);
    }

    /** @test */
    public function it_creates_achievement_on_fifty_lessons_watched()
    {
        $user = User::factory()->create();

        $this->create_prior_achievement($user, 'twenty_five_lessons_watched', 25, '25 Lessons Watched');
        $this->create_test_for_achievement('50 Lessons Watched', 50, $user);
    }

    public function create_prior_achievement(User $user, string $achievement_key, int $current_milestone, string $achievement_name)
    {
        $achievements = Achievement::factory()->create([
            'user_id' => $user->id,
            'achievement_key' => $achievement_key,
            'achievement_type' => 'lessons_watched',
            'current_milestone' => $current_milestone,
            'achievement_name' => $achievement_name,
        ]);
    }

    public function create_test_for_achievement(string $achievement_name, int $milestone, User $user)
    {
        Event::fake();

        $lessons = Lesson::factory()->count($milestone - 1)->create();
        $lessons->each(function ($lesson) use ($user) {
            $user->watched()->attach($lesson, ['watched' => true]);
        });

        $lesson = Lesson::factory()->create();
        $user->watched()->attach($lesson, ['watched' => true]);

        $event = new LessonWatched($lesson, $user);
        $listener = new LessonWatchedListener();
        $listener->handle($event);

        $this->assertDatabaseHas('achievements', [
            'user_id' => $user->id,
            'achievement_type' => 'lessons_watched',
            'current_milestone' => $milestone,
            'achievement_name' => $achievement_name,
        ]);

        Event::assertDispatched(AchievementUnlocked::class, function ($e) use ($user, $achievement_name) {
            return $e->user->id === $user->id && $e->achievementName === $achievement_name;
        });
    }
}
