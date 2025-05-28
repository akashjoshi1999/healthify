<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\UserActivityService;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get users with aggregated points and ranks, no filters initially
        $activities = $this->getUsersWithRanks();

        // For user filter dropdown
        $users = User::pluck('name', 'id')->toArray();

        return view('welcome', compact('activities', 'users'));
    }

    public function filter(Request $request)
    {
        // Extract filter inputs, including user_name for search
        $filters = $request->only(['user_id', 'year', 'month', 'day', 'user_name']);

        $activities = $this->getUsersWithRanks($filters);
        $users = User::pluck('name', 'id')->toArray();

        return view('welcome', compact('activities', 'users'));
    }

    /**
     * Get aggregated user points with rank, applying optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getUsersWithRanks(array $filters = [])
    {
        // Base query: group by user_id and sum points
        $query = UserActivity::select(
                    'user_id',
                    DB::raw('SUM(point) as total_points'),
                    DB::raw('MAX(created_at) as last_activity_date')
                )
                ->with('user')
                ->groupBy('user_id');

        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }
        if (!empty($filters['month'])) {
            $query->whereMonth('created_at', $filters['month']);
        }
        if (!empty($filters['day'])) {
            $query->whereDay('created_at', $filters['day']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['user_name'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['user_name'] . '%');
            });
        }

        // Order by total points descending
        $paginated = $query->orderByDesc('total_points')->paginate(10)->appends($filters);

        // Add rank attribute
        $rankStart = ($paginated->currentPage() - 1) * $paginated->perPage();
        $paginated->getCollection()->transform(function ($item, $index) use ($rankStart) {
            $item->rank = $rankStart + $index + 1;
            return $item;
        });

        return $paginated;
    }

    public function recalculate(Request $request, UserActivityService $service)
    {
        $service->generateActivities(20);
        $service->recalculateRanks();

        return redirect()->route('activities.index')->with('success', 'Activities recalculated and user ranks updated!');
    }
}
