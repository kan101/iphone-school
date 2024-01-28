<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Achievement;

class AchievementsController extends Controller
{
    public function index(User $user, Achievement $achievement)
    {
        $unlockedAchievements = $user->achievements()->pluck('achievement_name')->toArray();

        $nextLessonsAchievement = $user->nextAchievement('lessons_watched');
        $nextCommentsAchievement = $user->nextAchievement('comments_written');

        $nextAvailableAchievements = [$nextLessonsAchievement, $nextCommentsAchievement];
        $currentBadge = $user->getCurrentBadge()['name'];

        $next_badge = $user->getNextBadge($user->achievements()->count())['name'];
        $remainingToUnlockNextBadge = $user->remainingToUnlockNextBadge($user->achievements()->count());
        
        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge,
            'next_badge' => $next_badge,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge
        ]);
    }
}
