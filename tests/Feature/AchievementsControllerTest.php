<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\Achievement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AchievementsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_achievements()
    {
        /* Arrange: Create a user, lessons, and comments using factories */
        $user = User::factory()->create();

        /* Create lessons watched by the user */
        $lessons = Lesson::factory()->count(5)->create();
        $lessons->each(function ($lesson) use ($user) {
            $user->watched()->attach($lesson, ['watched' => true]);
        });

        /* Create comments written by the user */
        $comments = Comment::factory()->count(5)->create(['user_id' => $user->id]);

         /* create achievements */
         $achievements = Achievement::factory()->count(1)->create([
            'user_id' => $user->id,
            'achievement_key' => 'first_lesson_watched',
            'achievement_type' => 'lessons_watched',
            'current_milestone' => 1,
            'achievement_name' => 'First Lesson Watched'
        ]);

        $achievements = Achievement::factory()->count(1)->create([
            'user_id' => $user->id,
            'achievement_key' => 'five_lessons_watched',
            'achievement_type' => 'lessons_watched',
            'current_milestone' => 5,
            'achievement_name' => '5 Lessons Watched'
        ]);

        $achievements = Achievement::factory()->count(1)->create([
            'user_id' => $user->id,
            'achievement_key' => 'first_comment_written',
            'achievement_type' => 'comments_written',
            'current_milestone' => 1,
            'achievement_name' => 'First Comment Written'
        ]);

        $achievements = Achievement::factory()->count(1)->create([
            'user_id' => $user->id,
            'achievement_key' => 'three_comments_written',
            'achievement_type' => 'comments_written',
            'current_milestone' => 3,
            'achievement_name' => '3 Comments Written'
        ]);

        $achievements = Achievement::factory()->count(1)->create([
            'user_id' => $user->id,
            'achievement_key' => 'five_comments_written',
            'achievement_type' => 'comments_written',
            'current_milestone' => 5,
            'achievement_name' => '5 Comments Written'
        ]);


        /* Expected achievements based on the setup */
        $expectedUnlockedAchievements = [
            'First Lesson Watched',
            '5 Lessons Watched',
            'First Comment Written',
            '3 Comments Written',
            '5 Comments Written',
        ];

        /* Expected next achievements */
        $expectedNextAvailableAchievements = [
            '10 Lessons Watched',
            '10 Comments Written',
        ];

        /* Current and next badge expectations */
        $expectedCurrentBadge = 'Intermediate';
        $expectedNextBadge = 'Advanced';
        $expectedRemainingToUnlockNextBadge = 3;

        /* Act: Make a GET request to the achievements index route */
        $response = $this->actingAs($user)->getJson(route('achievements.index', $user));

        /* Assert: Check the structure and data of the response */
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
