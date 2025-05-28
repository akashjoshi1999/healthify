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
}
