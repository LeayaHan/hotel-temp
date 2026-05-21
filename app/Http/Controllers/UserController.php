<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        // Filter by role if provided
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderByRaw("FIELD(role, 'admin') ASC")->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        if (auth()->user()->role === 'staff') {
            abort(403, 'Housekeeping staff cannot create user accounts.');
        }

        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->role === 'staff') {
            abort(403, 'Housekeeping staff cannot create user accounts.');
        }

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'role'       => ['required', 'in:admin,manager,staff,front_desk,customer'],
            'department' => ['nullable', 'string', 'max:255'],
            'is_active'  => ['boolean'],
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'department' => $request->department,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Account for "' . $request->name . '" created successfully.');
    }

    /**
     * Display the specified user's profile.
     */
    public function show(User $user): View
    {
        $user->load(['guest.serviceRequests']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'       => ['required', 'in:admin,manager,staff,front_desk,customer'],
            'department' => ['nullable', 'string', 'max:255'],
            'is_active'  => ['boolean'],
            'password'   => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'department' => $request->department,
            'is_active'  => $request->boolean('is_active'),
        ];

        // Only update password if a new one was provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Account for "' . $user->name . '" updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Account for "' . $name . '" has been deleted.');
    }
}