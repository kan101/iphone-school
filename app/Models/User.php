<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The achievements that belong to the user.
     */
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    public function checkAndUnlockBadge()
    {
        $achievementCount = $this->achievements()->count();

        $milestones = [10, 8, 4, 0];

        if (in_array($achievementCount, $milestones)) {
            $this->triggerBadgeUnlocked($achievementCount);
        }
    }

    public function triggerBadgeUnlocked(int $achievementCount)
    {
        $badge = $this->determineBadge($achievementCount);
        $badge_name = $badge['name'];
        event(new BadgeUnlocked($badge_name, $this));
    }

    public function determineBadge(int $achievementCount): ?array
    {
        if ($achievementCount >= 10) {
            return $this->getAchievementDetails('badges', 'master');
        } elseif ($achievementCount >= 8) {
            return $this->getAchievementDetails('badges', 'advanced');
        } elseif ($achievementCount >= 4) {
            return $this->getAchievementDetails('badges', 'intermediate');
        } else {
            return $this->getAchievementDetails('badges', 'beginner');
        }
    }

    public function getNextBadge(int $achievementCount): ?array
    {
        $badge = $this->determineBadge($achievementCount);
        $next_badge = $this->getAchievementDetails('badges', $badge['next']);
        return $next_badge;
    }

    public function remainingToUnlockNextBadge(int $achievementCount): int
    {
        $currentBadgeDetails = $this->determineBadge($achievementCount);
        $nextBadgeKey = $currentBadgeDetails['next'] ?? null;

        if (is_null($nextBadgeKey)) {
            return 0;
        }

        $nextBadgeDetails = $this->getAchievementDetails('badges', $nextBadgeKey);

        $achievementsForNextBadge = $nextBadgeDetails['achievements'] ?? 0;
        $remainingToUnlockNextBadge = $achievementsForNextBadge - $achievementCount;

        return max($remainingToUnlockNextBadge, 0);
    }

    public function getCurrentBadge(): array
    {
        $achievementCount = $this->achievements()->count();
        return $this->determineBadge($achievementCount);
    }

    public function nextAchievement(string $achievementType)
    {
        $latestAchievement = Achievement::where('user_id', $this->id)
            ->where('achievement_type', $achievementType)->orderBy('current_milestone', 'desc')->first();

        if (is_null($latestAchievement)) {
            if ($achievementType == 'comments_written') {
                $achievement = $this->getAchievementDetails($achievementType, 'first_comment_written');
            } else {
                $achievement = $this->getAchievementDetails($achievementType, 'first_lesson_watched');
            }
        } else {
            $achievementDetails = $this->getAchievementDetails($achievementType, $latestAchievement->achievement_key);
            $achievement = $this->getAchievementDetails($achievementType, $achievementDetails['next']);
        }

        if (!is_null($achievement)) {
            return $achievement['name'];
        } else {
            return null;
        }
    }

    public function createAchievement(string $achievementKey, string $achievementType, int $milestone, string $achievementName)
    {
        $achievement = new Achievement([
            'user_id' => $this->id,
            'achievement_key' => $achievementKey,
            'achievement_type' => $achievementType,
            'achievement_name' => $achievementName,
            'current_milestone' => $milestone,
        ]);

        $achievement->save();
        $this->checkAndUnlockBadge();
        event(new AchievementUnlocked($achievementName, $this));
    }

    public function getAchievementDetails(string $achievementType, string $achievementKey): ?array
    {
        return config("achievements.{$achievementType}.{$achievementKey}");
    }

}
