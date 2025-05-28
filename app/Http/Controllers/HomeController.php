<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Models\User;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $activities = UserActivity::orderBy('created_at', 'desc')->paginate(10);
        $users = User::pluck('name', 'id')->toArray();
        
        return view('welcome', compact('activities', 'users'));
    }

    public function filter(Request $request)
    {
        $query = UserActivity::query();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('day')) {
            $query->whereDay('created_at', $request->day);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());
        $users = User::pluck('name', 'id')->toArray();

        return view('welcome', compact('activities', 'users'));
    }
}
