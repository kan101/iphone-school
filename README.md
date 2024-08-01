# Back-end Developer Test

## Timeline
- Expect to spend 4-6 hours working on the assignment

---

## Scenario

### Achievements

Our customers unlock free, purchased courses in our Course Portal.
Upon each unlocked course, a user also unlocks achievements.

#### Lesson Watched Achievements
- 1 Lesson Watched
- 5 Lessons Watched
- 10 Lessons Watched
- 25 Lessons Watched

#### Comments Written Achievements
- 1 Comment Written
- 5 Comments Written
- 10 Comments Written
- 25 Comments Written

User can have a badge, this is determined by the number of achievements they have unlocked.

#### Badges Achievements
- Novice: 1-4 achievements
- Advanced: 5-8 achievements
- Specialist: 9 achievements

---

## Your Assignment

### Unlocking Achievements
You need to write the code that listens to user events and unlocks the relevant achievements.

For example:
- When a user has commented for the first time you unlock the "1st Comment Written" achievement.
- When a user has already unlocked the "First Lesson Watched" achievement by watching a single lesson they watch another four lessons they unlock the "5 Lessons Watched" achievement.

### Achievement Unlocked Event
When an achievement is unlocked an AchievementUnlocked event must be fired with a payload of:


{
  "user": {User},
  "achievement": {Achievement}
}

### Badge Unlocked Event
When a user unlocks enough achievements to earn a new badge a BadgeUnlocked event must be fired with a payload of:

{
  "user": {User},
  "badge": {Badge}
}


## Achievement Blueprint

There are three fields in an achievement; these can be found in the `api/achievements` folder in the mock data file:

- `name` (type: string)
- `description` (type: string)
- `value` (type: int) - the user's unlocked achievements by name

## Badge Blueprint

- `name` (type: string)
- `max_level` (type: int)
- `achievements_needed` (type: int) - the number of achievements the user must unlock to earn the next badge

## Test Coverage

You should write tests that cover all possible scenarios.

Laravel HTTP tests documentation can be found at the following url: [https://laravel.com/docs/8.x/http-tests](https://laravel.com/docs/8.x/http-tests)

---

### User Events

The following event methods are available on the user model:

- `watchLesson()`
- `writeComment()`
  This will return an eloquent relationship for lessons watched by a user.

- `unwatchLesson()`
- `removeComment()`
  This will return an eloquent relationship for comments written by a user.

---