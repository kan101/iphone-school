<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Models\Achievement;
use Illuminate\Support\Facades\Config;
use App\Events\AchievementUnlocked;
use App\Models\User;

class CommentWrittenListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten $event): void
    {
        $user = $event->comment->user;
        $commentsCount = $user->comments()->count();

        $latestAchievement = Achievement::where('user_id', $user->id)
        ->where('achievement_type', 'comments_written')->orderBy('current_milestone', 'desc')->first();

        if ($latestAchievement) {
            /* Check if the user has reached the next milestone */
            $achievementDetails = $user->getAchievementDetails('comments_written', $latestAchievement->achievement_key);

            if ($commentsCount == $achievementDetails['next_milestone']) {
                $nextAchievementKey = $achievementDetails['next'];
                $nextAchievementDetails = $user->getAchievementDetails('comments_written', $nextAchievementKey);
                $user->createAchievement($nextAchievementKey, 'comments_written', $commentsCount, $nextAchievementDetails['name']);
            }
        } else {
            $user->createAchievement('first_comment_written', 'comments_written', 1, 'First Comment Written');
        }
    }
}
