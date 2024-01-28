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
            $achievementDetails = $user->getAchievementDetails('lessons_watched', $latestAchievement->achievement_key);

            if ($lessonsCount == $achievementDetails['next_milestone']) {
                $user->createAchievement($achievementDetails['next'], 'lessons_watched', $lessonsCount, $achievementDetails['next']['name']);
            }
        } else {
            $user->createAchievement('first_lesson_watched', 'lessons_watched', 1, 'First Lesson Watched');
        }
    }
}
