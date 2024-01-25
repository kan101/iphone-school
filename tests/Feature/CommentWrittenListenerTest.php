<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Listeners\CommentWrittenListener;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommentWrittenListenerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_achievement_on_first_comment_written()
    {
        Event::fake();

        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $event = new CommentWritten($comment);
        $listener = new CommentWrittenListener();
        $listener->handle($event);

        $this->assertDatabaseHas('achievements', [
            'user_id' => $user->id,
            'achievement_type' => 'comments_written',
        ]);

        Event::assertDispatched(AchievementUnlocked::class, function ($e) use ($user) {
            return $e->user->id === $user->id && $e->achievementName === 'First Comment Written';
        });
    }
}
