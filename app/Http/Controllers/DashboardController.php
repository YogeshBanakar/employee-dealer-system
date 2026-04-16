<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalUsers' => User::count(),
            'totalDealers' => User::where('user_type', 'dealer')->count(),
            'totalEmployees' => User::where('user_type', 'employee')->count(),
        ]);
    }
}
