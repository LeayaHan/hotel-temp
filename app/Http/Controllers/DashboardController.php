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

        // ── GUEST ──────────────────────────────────────────────────
        if ($role === 'customer' || $role === 'guest') {
            // Find the linked guest record via guest_id on the user
            // Use linked guest_id first, fall back to email match
            if ($user->guest_id) {
                $guest = Guest::find($user->guest_id);
            } else {
                $guest = Guest::where('email', $user->email)->first();
            }

            // Active requests (Open or In Progress)
            $recentRequests = $guest
                ? ServiceRequest::where('guest_id', $guest->id)
                    ->whereIn('status', ['Open', 'In Progress'])
                    ->latest()->take(5)->get()
                : collect();

            // History (Completed or Cancelled) — with optional filters
            $historyQuery = $guest
                ? ServiceRequest::where('guest_id', $guest->id)
                    ->whereIn('status', ['Completed', 'Cancelled'])
                : ServiceRequest::whereRaw('0=1');

            $historyStatus      = request('history_status');
            $historyServiceType = request('history_service_type');
            $historyPriority    = request('history_priority');

            if ($historyStatus && in_array($historyStatus, ['Completed', 'Cancelled'])) {
                $historyQuery->where('status', $historyStatus);
            }
            if ($historyServiceType) {
                $historyQuery->where('service_type', $historyServiceType);
            }
            if ($historyPriority) {
                $historyQuery->where('priority', $historyPriority);
            }

            $historyRequests = $historyQuery->latest()->paginate(10, ['*'], 'history_page');

            // Distinct service types the guest has used (for filter dropdown)
            $historyServiceTypes = $guest
                ? ServiceRequest::where('guest_id', $guest->id)
                    ->whereIn('status', ['Completed', 'Cancelled'])
                    ->distinct()->orderBy('service_type')->pluck('service_type')
                : collect();

            return view('dashboard', compact(
                'recentRequests', 'historyRequests', 'historyServiceTypes',
                'historyStatus', 'historyServiceType', 'historyPriority', 'guest'
            ));
        }

        // ── STAFF ──────────────────────────────────────────────────
        if ($role === 'staff') {
            $openRequests    = ServiceRequest::where('status', 'Open')->count();
            $inProgress      = ServiceRequest::where('status', 'In Progress')->count();
            $completedToday  = ServiceRequest::where('status', 'Completed')
                                    ->whereDate('updated_at', today())->count();
            $totalGuests     = Guest::count();
            $pendingRequests = ServiceRequest::with('guest')
                                ->whereIn('status', ['Open', 'In Progress'])
                                ->latest()->take(10)->get();

            $historyRequests = ServiceRequest::with('guest')
                                ->whereIn('status', ['Completed', 'Cancelled'])
                                ->latest()->take(20)->get();

            $guests = Guest::withCount([
                'serviceRequests as total_requests',
                'serviceRequests as open_requests' => fn($q) =>
                    $q->whereIn('status', ['Open', 'In Progress']),
            ])->latest()->get();

            return view('dashboard', compact(
                'openRequests', 'inProgress', 'completedToday',
                'totalGuests', 'pendingRequests', 'historyRequests', 'guests'
            ));
        }

        // ── FRONT DESK ─────────────────────────────────────────────
        if ($role === 'front_desk') {
            $openRequests    = ServiceRequest::where('status', 'Open')->count();
            $inProgress      = ServiceRequest::where('status', 'In Progress')->count();
            $completedToday  = ServiceRequest::where('status', 'Completed')
                                    ->whereDate('updated_at', today())->count();
            $totalGuests     = Guest::count();
            $pendingRequests = ServiceRequest::with('guest')
                                ->whereIn('status', ['Open', 'In Progress'])
                                ->latest()->take(10)->get();

            return view('dashboard', compact(
                'openRequests', 'inProgress', 'completedToday',
                'totalGuests', 'pendingRequests'
            ));
        }

        // ── ADMIN ──────────────────────────────────────────────────
        if ($role === 'admin') {
            $totalGuests    = Guest::count();
            $checkedIn      = Guest::where('status', 'Checked In')->count();
            $openRequests   = ServiceRequest::where('status', 'Open')->count();
            $completedToday = ServiceRequest::where('status', 'Completed')
                                    ->whereDate('updated_at', today())->count();

            $recentRequests = ServiceRequest::with(['guest', 'user'])
                                ->whereIn('status', ['Open', 'In Progress'])
                                ->latest()->take(5)->get();

            $historyRequests = ServiceRequest::with(['guest', 'user'])
                                ->whereIn('status', ['Completed', 'Cancelled'])
                                ->latest()->take(10)->get();

            $recentUsers = User::latest()->take(5)->get();

            return view('dashboard', compact(
                'totalGuests', 'checkedIn', 'openRequests', 'completedToday',
                'recentRequests', 'historyRequests', 'recentUsers'
            ));
        }

        // ── FALLBACK ───────────────────────────────────────────────
        return view('dashboard', [
            'recentRequests'  => collect(),
            'historyRequests' => collect(),
            'guest'           => null,
        ]);
    }
}