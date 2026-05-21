<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['guest', 'user'])
            ->when(!$request->filled('status'), function ($q) {
                $q->whereNotIn('status', ['Completed', 'Cancelled']);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('service_type', 'like', "%{$search}%")
                  ->orWhereHas('guest', fn($g) => $g->where('full_name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('guest_id')) {
            $query->where('guest_id', $request->guest_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $serviceRequests = $query->latest()->paginate(10)->withQueryString();
        $guests = Guest::orderBy('full_name')->get();

        $serviceTypes = ServiceRequest::select('service_type')->distinct()->orderBy('service_type')->pluck('service_type');
        $floors       = ServiceRequest::select('floor')->distinct()->whereNotNull('floor')->orderBy('floor')->pluck('floor');

        return view('service-requests.index', compact('serviceRequests', 'guests', 'serviceTypes', 'floors'));
    }

    public function history(Request $request)
    {
        $query = ServiceRequest::with(['guest', 'user'])
            ->whereIn('status', ['Completed', 'Cancelled']);

        if ($request->filled('guest_id')) {
            $query->where('guest_id', $request->guest_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        if ($request->filled('room')) {
            $query->where('room_number', 'like', "%{$request->room}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $serviceRequests = $query->latest()->paginate(15)->withQueryString();

        $guests       = Guest::orderBy('full_name')->get();
        $serviceTypes = ServiceRequest::select('service_type')->distinct()->orderBy('service_type')->pluck('service_type');
        $floors       = ServiceRequest::select('floor')->distinct()->whereNotNull('floor')->orderBy('floor')->pluck('floor');

        return view('service-requests.history', compact('serviceRequests', 'guests', 'serviceTypes', 'floors'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('service-requests.index')
                             ->with('error', 'Admins can only view service requests.');
        }

        $guests = Guest::orderBy('full_name')->get();
        return view('service-requests.create', compact('guests'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('service-requests.index')
                             ->with('error', 'Admins can only view service requests.');
        }

        $request->validate([
            'guest_id'     => 'required|exists:guests,id',
            'service_type' => 'required|string',
        ]);

        $guest = Guest::find($request->guest_id);

        ServiceRequest::create([
            'guest_id'     => $request->guest_id,
            'user_id'      => auth()->id(),
            'floor'        => $request->floor ?? null,
            'room_number'  => $request->room_number ?? $guest->room_number ?? null,
            'service_type' => $request->service_type,
            'details'      => $request->details,
            'priority'     => $request->priority ?? 'Low',
            'status'       => $request->status ?? 'Open',
            'quantities'   => $request->quantities,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('service-requests.index')
                         ->with('success', 'Service request created.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['guest', 'user']);
        return view('service-requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('service-requests.show', $serviceRequest)
                             ->with('error', 'Admins can only view service requests.');
        }

        $serviceRequest->load('guest');
        $guests = Guest::orderBy('full_name')->get();
        return view('service-requests.edit', compact('serviceRequest', 'guests'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('service-requests.show', $serviceRequest)
                             ->with('error', 'Admins can only view service requests.');
        }

        $request->validate([
            'status' => 'required|string|in:Open,In Progress,Completed,Cancelled',
        ]);

        $serviceRequest->update([
            'status' => $request->status,
        ]);

        return redirect()->route('service-requests.index')
                         ->with('success', 'Service request updated.');
    }

    public function destroy(Request $request, ServiceRequest $serviceRequest)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('service-requests.index')
                             ->with('error', 'Admins can only view service requests.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ], [
            'cancellation_reason.required' => 'Please provide a reason for cancellation.',
        ]);

        $serviceRequest->update([
            'status'              => 'Cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()->route('service-requests.index')
                         ->with('success', 'Service request cancelled.');
    }
}