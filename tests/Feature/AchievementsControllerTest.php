<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_achievements()
    {
        $user = User::factory()->create();

        $lessons = Lesson::factory()->count(5)->create();
        $lessons->each(function ($lesson) use ($user) {
            $user->watched()->attach($lesson, ['watched' => true]);
        });

        $comments = Comment::factory()->count(5)->create(['user_id' => $user->id]);

        /** create achievements setup*/
        $this->create_achievement($user, 'first_lesson_watched', 'lessons_watched', 1, 'First Lesson Watched');
        $this->create_achievement($user, 'five_lessons_watched', 'lessons_watched', 5, '5 Lessons Watched');
        $this->create_achievement($user, 'first_comment_written', 'comments_written', 1, 'First Comment Written');
        $this->create_achievement($user, 'three_comments_written', 'comments_written', 3, '3 Comments Written');
        $this->create_achievement($user, 'five_comments_written', 'comments_written', 5, '5 Comments Written');

        /** Expected achievements based on the setup */
        $expectedUnlockedAchievements = [
            'First Lesson Watched',
            '5 Lessons Watched',
            'First Comment Written',
            '3 Comments Written',
            '5 Comments Written',
        ];

        /** Expected next achievements */
        $expectedNextAvailableAchievements = [
            '10 Lessons Watched',
            '10 Comments Written',
        ];

        /** Current and next badge expectations */
        $expectedCurrentBadge = 'Intermediate';
        $expectedNextBadge = 'Advanced';
        $expectedRemainingToUnlockNextBadge = 3;

        /** Act: Make a GET request to the achievements index route */
        $response = $this->actingAs($user)->getJson(route('achievements.index', $user));

        $response->assertOk()
            ->assertJson([
                'unlocked_achievements' => $expectedUnlockedAchievements,
                'next_available_achievements' => $expectedNextAvailableAchievements,
                'current_badge' => $expectedCurrentBadge,
                'next_badge' => $expectedNextBadge,
                'remaining_to_unlock_next_badge' => $expectedRemainingToUnlockNextBadge,
            ]);
    }
}
