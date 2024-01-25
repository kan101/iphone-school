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
            ->where('achievement_type', 'comments_written')
            ->latest()
            ->first();

        if ($latestAchievement) {
            /* Check if the user has reached the next milestone */
            $achievementDetails = $this->getAchievementDetails('comments_written', $latestAchievement->achievementKey);

            if ($commentsCount == $achievementDetails['next_milestone']) {
                $this->createAchievement($user, $achievementDetails['next'], 'comments_written', $commentsCount);
            }
        } else {
            $this->createAchievement($user, 'first_comment_written', 'comments_written', 1, 'First Comment Written');
        }
    }

    private function createAchievement(User $user, string $achievementKey, string $achievementType, int $milestone, string $achievementName)
    {
        $achievement = new Achievement([
            'user_id' => $user->id,
            'achievement_key' => $achievementKey,
            'achievement_type' => $achievementType,
            'current_milestone' => $milestone,
        ]);

        $achievement->save();
        event(new AchievementUnlocked($achievementName, $user));
    }

    private function getAchievementDetails(string $achievementType, string $achievementKey): ?array
    {
        return Config::get("achievements.{$achievementType}.{$achievementKey}");
    }
}
