<?php

return [

    'lessons_watched' => [
        'first_lesson_watched' => ['name' => 'First Lesson Watched', 'milestone' => 1, 'next' => '5 Lessons Watched'],
        'five_lessons_watched' => ['name' => '5 Lessons Watched', 'milestone' => 5, 'next' => '10 Lessons Watched'],
        'ten_lessons_watched' => ['name' => '10 Lessons Watched', 'milestone' => 10, 'next' => '25 Lessons Watched'],
        'twenty_five_lessons_watched' => ['name' => '25 Lessons Watched', 'milestone' => 25, 'next' => '50 Lessons Watched'],
        'fifty_lessons_watched' => ['name' => '50 Lessons Watched', 'milestone' => 50, 'next' => null], // No next achievement
    ],

    'comments_written' => [
        'first_comment_written' => ['name' => 'First Comment Written', 'milestone' => 1, 'next' => 'three_comments_written', 'next_milestone' => 3],
        'three_comments_written' => ['name' => '3 Comments Written', 'milestone' => 3, 'next' => 'five_comments_written', 'next_milestone' => 5],
        'five_comments_written' => ['name' => '5 Comments Written', 'milestone' => 5, 'next' => 'ten_comments_written', 'next_milestone' => 10],
        'ten_comments_written' => ['name' => '10 Comments Written', 'milestone' => 10, 'next' => 'twenty_comments_written', 'next_milestone' => 20],
        'twenty_comments_written' => ['name' => '20 Comments Written', 'milestone' => 20, 'next' => null, 'next_milestone' => null], // No next achievement
    ],

    'badges' => [
        'beginner' => ['name' => 'Beginner', 'achievements' => 0, 'next' => 'Intermediate'],
        'intermediate' => ['name' => 'Intermediate', 'achievements' => 4, 'next' => 'Advanced'],
        'advanced' => ['name' => 'Advanced', 'achievements' => 8, 'next' => 'Master'],
        'master' => ['name' => 'Master', 'achievements' => 10, 'next' => null], // No next badge
    ],

];
