<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $serviceRequests = ServiceRequest::with('guest')->latest()->paginate(10);
        $guests = Guest::orderBy('full_name')->get();
        return view('service-requests.index', compact('serviceRequests', 'guests'));
    }

    public function create()
    {
        $guests = Guest::orderBy('full_name')->get();
        return view('service-requests.create', compact('guests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_id'     => 'required|exists:guests,id',
            'service_type' => 'required|string',
        ]);

        ServiceRequest::create([
            'guest_id'     => $request->guest_id,
            'user_id'      => auth()->id(),
            'service_type' => $request->service_type,
            'details'      => $request->details,
            'priority'     => $request->priority ?? 'Normal',
            'status'       => $request->status ?? 'Open',
        ]);

        return redirect()->route('service-requests.index')
                         ->with('success', 'Service request created.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        return view('service-requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $guests = Guest::orderBy('full_name')->get();
        return view('service-requests.edit', compact('serviceRequest', 'guests'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'service_type' => 'required|string',
        ]);

        $serviceRequest->update($request->only([
            'guest_id', 'service_type', 'details', 'priority', 'status'
        ]));

        return redirect()->route('service-requests.index')
                         ->with('success', 'Service request updated.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();
        return redirect()->route('service-requests.index')
                         ->with('success', 'Service request deleted.');
    }
}