<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement_key',
        'achievement_type',
        'current_milestone',
    ];

    /**
     * Define the relationship with the User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAchievementNamesByKeys($keys)
    {
        $names = [];

        foreach ($keys as $key) {
            if (isset($achievements[$key])) {
                $names[] = $achievements[$key]['name'];
            }
        }

        return $names;
    }
}
