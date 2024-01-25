<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Achievement;
use Illuminate\Support\Facades\Config;
use App\Events\AchievementUnlocked;
use App\Models\User;

class LessonWatchedListener
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
    public function handle(LessonWatched $event): void
    {
        $user = $event->user;
        $lessonsCount = $user->watched()->count();

        $latestAchievement = Achievement::where('user_id', $user->id)
            ->where('achievement_type', 'lessons_watched')
            ->latest()
            ->first();

        if ($latestAchievement) {
            /* Check if the user has reached the next milestone */
            $achievementDetails = $this->getAchievementDetails('lessons_watched', $latestAchievement->achievementKey);

            if ($lessonsCount == $achievementDetails['next_milestone']) {
                $this->createAchievement($user, $achievementDetails['next'], 'lessons_watched', $lessonsCount);
            }
        } else {
            $this->createAchievement($user, 'first_lesson_watched', 'lessons_watched', 1, 'First Lesson Watched');
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
