<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MyServiceRequestController extends Controller
{
    const QUANTITY_SERVICES = [
        'Extra Towels / Linens',
        'Room Service / Food',
        'Laundry Service',
        'Toiletries',
    ];

    const SERVICE_ITEMS = [
        'Extra Towels / Linens' => ['Bath Towel', 'Hand Towel', 'Face Towel', 'Bed Sheet', 'Pillowcase', 'Blanket'],
        'Room Service / Food'   => ['Water Bottle', 'Coffee', 'Tea', 'Breakfast Set', 'Lunch Set', 'Dinner Set', 'Snack Pack'],
        'Laundry Service'       => ['Shirt', 'Pants', 'Dress', 'Underwear', 'Socks', 'Jacket'],
        'Toiletries'            => ['Shampoo', 'Conditioner', 'Soap', 'Toothbrush', 'Toothpaste', 'Razor', 'Lotion', 'Shower Cap'],
    ];

    private function getGuestForUser()
    {
        $user = auth()->user();
        // Use linked guest_id first, fall back to email match
        if ($user->guest_id) {
            return Guest::find($user->guest_id);
        }
        return Guest::where('email', $user->email)->first();
    }

    public function index(Request $request)
    {
        $guest = $this->getGuestForUser();

        $query = $guest
            ? ServiceRequest::where('guest_id', $guest->id)->whereIn('status', ['Open', 'In Progress'])
            : ServiceRequest::where('user_id', auth()->id())->whereIn('status', ['Open', 'In Progress']);

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $serviceRequests = $query->latest()->paginate(10)->withQueryString();

        return view('my-services.index', compact('serviceRequests'));
    }

    public function history(Request $request)
    {
        $guest = $this->getGuestForUser();

        $query = $guest
            ? ServiceRequest::where('guest_id', $guest->id)->whereIn('status', ['Completed', 'Cancelled'])
            : ServiceRequest::where('user_id', auth()->id())->whereIn('status', ['Completed', 'Cancelled']);

        if ($request->filled('history_status') && in_array($request->history_status, ['Completed', 'Cancelled'])) {
            $query->where('status', $request->history_status);
        }
        if ($request->filled('history_service_type')) {
            $query->where('service_type', $request->history_service_type);
        }
        if ($request->filled('history_priority')) {
            $query->where('priority', $request->history_priority);
        }

        $historyRequests = $query->latest()->paginate(10);

        $historyServiceTypes = $guest
            ? ServiceRequest::where('guest_id', $guest->id)
                ->whereIn('status', ['Completed', 'Cancelled'])
                ->distinct()->orderBy('service_type')->pluck('service_type')
            : collect();

        return view('my-services.history', compact(
            'historyRequests', 'historyServiceTypes',
        ));
    }

    public function create()
    {
        $guest = $this->getGuestForUser();

        return view('my-services.create', [
            'quantityServices' => self::QUANTITY_SERVICES,
            'serviceItems'     => self::SERVICE_ITEMS,
            'guestFloor'       => $guest?->floor_number ?? null,
            'guestRoom'        => $guest?->room_number ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'floor'        => 'required|string|max:50',
            'room_number'  => 'required|string|max:50',
            'service_type' => 'required|string',
            'priority'     => 'required|in:Urgent,Scheduled,Low',
            'scheduled_at' => 'required_if:priority,Scheduled|nullable|date|after:now',
        ], [
            'floor.required'           => 'Please enter your floor number.',
            'room_number.required'     => 'Please enter your room number.',
            'scheduled_at.required_if' => 'Please choose a date and time for the scheduled request.',
            'scheduled_at.after'       => 'Scheduled time must be in the future.',
        ]);

        $quantities = null;
        if (in_array($request->service_type, self::QUANTITY_SERVICES)) {
            $items = self::SERVICE_ITEMS[$request->service_type] ?? [];
            $qty = [];
            foreach ($items as $item) {
                $key = 'qty_' . Str::slug($item, '_');
                $val = (int) $request->input($key, 0);
                if ($val > 0) {
                    $qty[$item] = $val;
                }
            }
            $quantities = !empty($qty) ? $qty : null;
        }

        $guest = $this->getGuestForUser();

        ServiceRequest::create([
            'guest_id'     => $guest?->id,
            'user_id'      => auth()->id(),
            'floor'        => $request->floor,
            'room_number'  => $request->room_number,
            'service_type' => $request->service_type,
            'details'      => $request->details,
            'priority'     => $request->priority,
            'status'       => 'Open',
            'quantities'   => $quantities,
            'scheduled_at' => $request->priority === 'Scheduled' ? $request->scheduled_at : null,
        ]);

        return redirect()->route('my-services.index')
                         ->with('success', 'Request submitted successfully.');
    }

    public function show(ServiceRequest $myService)
    {
        $serviceRequest = $myService;
        return view('my-services.show', [
            'serviceRequest' => $serviceRequest,
            'serviceItems'   => self::SERVICE_ITEMS,
        ]);
    }

    public function edit(ServiceRequest $myService)
    {
        // Only allow editing if the request is still Open
        if ($myService->status !== 'Open') {
            return redirect()->route('my-services.index')
                             ->with('error', 'This request cannot be edited because it is already ' . $myService->status . '.');
        }

        $serviceRequest = $myService;
        return view('my-services.edit', [
            'serviceRequest'   => $serviceRequest,
            'quantityServices' => self::QUANTITY_SERVICES,
            'serviceItems'     => self::SERVICE_ITEMS,
        ]);
    }

    public function update(Request $request, ServiceRequest $myService)
    {
        // Double-check server-side in case someone bypasses the UI
        if ($myService->status !== 'Open') {
            return redirect()->route('my-services.index')
                             ->with('error', 'This request cannot be edited because it is already ' . $myService->status . '.');
        }

        $request->validate([
            'floor'        => 'required|string|max:50',
            'room_number'  => 'required|string|max:50',
            'service_type' => 'required|string',
            'priority'     => 'required|in:Urgent,Scheduled,Low',
            'scheduled_at' => 'required_if:priority,Scheduled|nullable|date|after:now',
        ], [
            'floor.required'           => 'Please enter your floor number.',
            'room_number.required'     => 'Please enter your room number.',
            'scheduled_at.required_if' => 'Please choose a date and time for the scheduled request.',
            'scheduled_at.after'       => 'Scheduled time must be in the future.',
        ]);

        $quantities = null;
        if (in_array($request->service_type, self::QUANTITY_SERVICES)) {
            $items = self::SERVICE_ITEMS[$request->service_type] ?? [];
            $qty = [];
            foreach ($items as $item) {
                $key = 'qty_' . Str::slug($item, '_');
                $val = (int) $request->input($key, 0);
                if ($val > 0) {
                    $qty[$item] = $val;
                }
            }
            $quantities = !empty($qty) ? $qty : null;
        }

        $myService->update([
            'floor'        => $request->floor,
            'room_number'  => $request->room_number,
            'service_type' => $request->service_type,
            'details'      => $request->details,
            'priority'     => $request->priority,
            'quantities'   => $quantities,
            'scheduled_at' => $request->priority === 'Scheduled' ? $request->scheduled_at : null,
        ]);

        return redirect()->route('my-services.index')
                         ->with('success', 'Request updated.');
    }

    public function destroy(Request $request, ServiceRequest $myService)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ], [
            'cancellation_reason.required' => 'Please provide a reason for cancellation.',
        ]);

        $myService->update([
            'status'              => 'Cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()->route('my-services.index')
                         ->with('success', 'Request cancelled.');
    }
}