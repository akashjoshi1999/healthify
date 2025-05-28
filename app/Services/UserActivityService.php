<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserActivityService
{
    public function generateActivities($count = 20)
    {
        $faker = Faker::create();
        $userIds = User::inRandomOrder()->limit(15)->pluck('id');

        foreach (range(1, $count) as $i) {
            UserActivity::create([
                'user_id' => $userIds->random(),
                'point' => 20,
                'created_at' => $faker->dateTimeBetween('-3 year', 'now'),
            ]);
        }
    }

    public function recalculateRanks()
    {
        $userRanks = $this->calculateUserRanks();

        foreach ($userRanks as $userId => $rank) {
            User::where('id', $userId)->update(['rank' => $rank]);
        }

        User::whereNotIn('id', $userRanks->keys())->update(['rank' => null]);
    }

    public function calculateUserRanks()
    {
        return UserActivity::selectRaw('user_id, SUM(point) as total_points')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->pluck('total_points', 'user_id')
            ->mapWithKeys(function ($points, $index) {
                static $rank = 1;
                return [$index => $rank++];
            });
    }
}
