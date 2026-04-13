<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role ?? 'guest';

        if ($role === 'guest') {
            $recentRequests = ServiceRequest::where('user_id', $user->id)
                                ->latest()->take(5)->get();
            return view('dashboard', compact('recentRequests'));
        }

        if ($role === 'staff') {
            $openRequests    = ServiceRequest::where('status', 'Open')->count();
            $inProgress      = ServiceRequest::where('status', 'In Progress')->count();
            $completedToday  = ServiceRequest::where('status', 'Completed')->whereDate('updated_at', today())->count();
            $totalGuests     = Guest::count();
            $pendingRequests = ServiceRequest::with('guest')
                                ->whereIn('status', ['Open', 'In Progress'])
                                ->latest()->take(10)->get();

            return view('dashboard', compact(
                'openRequests', 'inProgress', 'completedToday',
                'totalGuests', 'pendingRequests'
            ));
        }

        if ($role === 'admin') {
            $totalGuests    = Guest::count();
            $checkedIn      = Guest::where('status', 'Checked In')->count();
            $openRequests   = ServiceRequest::where('status', 'Open')->count();
            $completedToday = ServiceRequest::where('status', 'Completed')->whereDate('updated_at', today())->count();
            $recentRequests = ServiceRequest::with('guest')->latest()->take(5)->get();
            $recentUsers    = User::latest()->take(5)->get();

            return view('dashboard', compact(
                'totalGuests', 'checkedIn', 'openRequests',
                'completedToday', 'recentRequests', 'recentUsers'
            ));
        }

        return view('dashboard', ['recentRequests' => collect()]);
    }
}