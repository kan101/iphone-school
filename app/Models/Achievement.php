<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement_key',
        'achievement_name',
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
}
