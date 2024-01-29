<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Models\Achievement;
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
            ->where('achievement_type', 'lessons_watched')->orderBy('current_milestone', 'desc')->first();

        if ($latestAchievement) {
            /* Check if the user has reached the next milestone */
            $achievementDetails = $user->getAchievementDetails('lessons_watched', $latestAchievement->achievement_key);

            if ($lessonsCount == $achievementDetails['next_milestone']) {
                $nextAchievementKey = $achievementDetails['next'];
                $nextAchievementDetails = $user->getAchievementDetails('lessons_watched', $nextAchievementKey);
                $user->createAchievement($nextAchievementKey, 'lessons_watched', $lessonsCount, $nextAchievementDetails['name']);
            }
        } else {
            $user->createAchievement('first_lesson_watched', 'lessons_watched', 1, 'First Lesson Watched');
        }
    }
}
