<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Listeners\CommentWrittenListener;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommentWrittenListenerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_listener_creates_achievement_on_first_comment_written()
    {
        $user = User::factory()->create();
        $this->create_test_for_achievement('First Comment Written', 1, $user);
    }

    /** @test */
    public function test_listener_creates_achievement_on_three_comments_written()
    {
        $user = User::factory()->create();

        /* setup test database with achievement before the one being tested */
        $this->create_achievement($user, 'first_comment_written', 'comments_written', 1, 'First Comment Written');
        $this->create_test_for_achievement('3 Comments Written', 3, $user);
    }

    /** @test */
    public function test_listener_creates_achievement_on_five_comments_written()
    {
        $user = User::factory()->create();

        $this->create_achievement($user, 'three_comments_written', 'comments_written', 3, '3 Comments Written');
        $this->create_test_for_achievement('5 Comments Written', 5, $user);
    }

    /** @test */
    public function test_listener_creates_achievement_on_ten_comments_written()
    {
        $user = User::factory()->create();

        $this->create_achievement($user, 'five_comments_written', 'comments_written', 5, '5 Comments Written');
        $this->create_test_for_achievement('10 Comments Written', 10, $user);
    }

    /** @test */
    public function test_listener_creates_achievement_on_twenty_comments_written()
    {
        $user = User::factory()->create();

        $this->create_achievement($user, 'ten_comments_written', 'comments_written', 10, '10 Comments Written');
        $this->create_test_for_achievement('20 Comments Written', 20, $user);
    }

    public function create_test_for_achievement(string $achievement_name, int $milestone, User $user)
    {
        Event::fake([AchievementUnlocked::class]);
        /** populate db with comments one less than the milestone that triggers achievement */
        $comments = Comment::factory()->count($milestone - 1)->create(['user_id' => $user->id]);

        /** create comment that triggers achievement event*/
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $event = new CommentWritten($comment);
        $listener = app(CommentWrittenListener::class);
        $listener->handle($event);

        $this->assertDatabaseHas('achievements', [
            'user_id' => $user->id,
            'achievement_type' => 'comments_written',
            'current_milestone' => $milestone,
            'achievement_name' => $achievement_name
        ]);

        Event::assertDispatched(AchievementUnlocked::class, function ($e) use ($user, $achievement_name) {
            return $e->user->id === $user->id && $e->achievementName === $achievement_name;
        });
    }
}
