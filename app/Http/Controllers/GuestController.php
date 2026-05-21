<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'first_name'     => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:5',
            'last_name'      => 'required|string|max:255',
            'email'          => 'nullable|email|unique:guests,email|unique:users,email',
            'phone'          => 'nullable|string|max:50',
            'room_number'    => 'nullable|string|max:50',
            'floor_number'   => 'nullable|string|max:50',
            'check_in'       => 'nullable|date',
            'check_out'      => 'nullable|date|after_or_equal:check_in',
            'status'         => 'required|string|max:50',
        ]);

        $guest = Guest::create($request->only([
            'first_name', 'middle_initial', 'last_name',
            'email', 'phone', 'room_number', 'floor_number',
            'check_in', 'check_out', 'status',
        ]));

        // Auto-create a user account if email is provided
        if ($guest->email) {
            $tempPassword = Str::random(10);

            User::create([
                'name'      => $guest->full_name,
                'email'     => $guest->email,
                'password'  => Hash::make($tempPassword),
                'role'      => 'customer',
                'is_active' => true,
                'guest_id'  => $guest->id,
            ]);

            session()->flash('temp_password', $tempPassword);
            session()->flash('temp_email', $guest->email);
        }

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
            'first_name'     => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:5',
            'last_name'      => 'required|string|max:255',
            'email'          => 'nullable|email|unique:guests,email,' . $guest->id,
            'phone'          => 'nullable|string|max:50',
            'room_number'    => 'nullable|string|max:50',
            'floor_number'   => 'nullable|string|max:50',
            'check_in'       => 'nullable|date',
            'check_out'      => 'nullable|date|after_or_equal:check_in',
            'status'         => 'required|string|max:50',
        ]);

        $guest->update($request->only([
            'first_name', 'middle_initial', 'last_name',
            'email', 'phone', 'room_number', 'floor_number',
            'check_in', 'check_out', 'status',
        ]));

        return redirect()->route('guests.index')->with('success', 'Guest updated successfully.');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully.');
    }
}