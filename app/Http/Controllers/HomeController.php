<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Services\UserActivityService;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $activities = $this->getActivitiesWithRank();
        $users = User::pluck('name', 'id')->toArray();

        return view('welcome', compact('activities', 'users'));
    }

    public function filter(Request $request)
    {
        $filters = $request->only(['user_id', 'year', 'month', 'day']);
        $activities = $this->getActivitiesWithRank($filters);
        $users = User::pluck('name', 'id')->toArray();

        return view('welcome', compact('activities', 'users'));
    }

    private function getActivitiesWithRank(array $filters = [])
    {
        $query = UserActivity::with('user');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }
        if (!empty($filters['month'])) {
            $query->whereMonth('created_at', $filters['month']);
        }
        if (!empty($filters['day'])) {
            $query->whereDay('created_at', $filters['day']);
        }

        $activities = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($filters);

        $userRanks = $this->calculateUserRanks();

        foreach ($activities as $activity) {
            $activity->rank = $userRanks[$activity->user_id] ?? null;
        }

        return $activities;
    }

    private function calculateUserRanks()
    {
        return UserActivity::select('user_id', DB::raw('SUM(point) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get()
            ->mapWithKeys(function ($item, $index) {
                return [$item->user_id => $index + 1];
            });
    }

    public function recalculate(Request $request, UserActivityService $service)
    {
        $service->generateActivities(20);
        $service->recalculateRanks();

        return redirect()->route('activities.index')->with('success', 'Activities recalculated and user ranks updated!');
    }
}
