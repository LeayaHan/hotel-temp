<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::latest()->paginate(10);
        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        return view('guests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:guests,email',
            'phone'       => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:50',
            'check_in'    => 'nullable|date',
            'check_out'   => 'nullable|date|after_or_equal:check_in',
            'status'      => 'required|string|max:50',
        ]);

        Guest::create($request->all());

        return redirect()->route('guests.index')->with('success', 'Guest created successfully.');
    }

    public function show(Guest $guest)
    {
        return view('guests.show', compact('guest'));
    }

    public function edit(Guest $guest)
    {
        return view('guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        $request->validate([
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:guests,email,' . $guest->id,
            'phone'       => 'nullable|string|max:50',
            'room_number' => 'nullable|string|max:50',
            'check_in'    => 'nullable|date',
            'check_out'   => 'nullable|date|after_or_equal:check_in',
            'status'      => 'required|string|max:50',
        ]);

        $guest->update($request->all());

        return redirect()->route('guests.index')->with('success', 'Guest updated successfully.');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();

        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully.');
    }
}