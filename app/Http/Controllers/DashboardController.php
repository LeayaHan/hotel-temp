<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $recentRequests = ServiceRequest::where('user_id', $user->id)
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard', compact('recentRequests'));
    }
}