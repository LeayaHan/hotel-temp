<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MyServiceRequestController extends Controller
{
    private function getGuestForUser()
    {
        return Guest::where('email', auth()->user()->email)->first();
    }

    public function index()
    {
        $guest = $this->getGuestForUser();

        if ($guest) {
            $serviceRequests = ServiceRequest::where('guest_id', $guest->id)
                                ->latest()->paginate(10);
        } else {
            $serviceRequests = ServiceRequest::where('user_id', auth()->id())
                                ->latest()->paginate(10);
        }

        return view('my-services.index', compact('serviceRequests'));
    }

    public function create()
    {
        return view('my-services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
        ]);

        $guest = $this->getGuestForUser();

        ServiceRequest::create([
            'guest_id'     => $guest?->id,
            'user_id'      => auth()->id(),
            'service_type' => $request->service_type,
            'details'      => $request->details,
            'priority'     => $request->priority ?? 'Normal',
            'status'       => 'Open',
        ]);

        return redirect()->route('my-services.index')
                         ->with('success', 'Request submitted successfully.');
    }

    public function show(ServiceRequest $myService)
    {
        $serviceRequest = $myService;
        return view('my-services.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $myService)
    {
        $serviceRequest = $myService;
        return view('my-services.edit', compact('serviceRequest'));
    }

    public function update(Request $request, ServiceRequest $myService)
    {
        $request->validate([
            'service_type' => 'required|string',
        ]);

        $myService->update($request->only([
            'service_type', 'details', 'priority'
        ]));

        return redirect()->route('my-services.index')
                         ->with('success', 'Request updated.');
    }

    public function destroy(ServiceRequest $myService)
    {
        $myService->delete();
        return redirect()->route('my-services.index')
                         ->with('success', 'Request cancelled.');
    }
}