@extends('layouts.app')

@section('content')

<div class="page-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>Users</h1>
            <p>Manage all system users — guests, staff, and admins.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add User
        </a>
    </div>
</div>

@if($users->isEmpty())
    <div class="card">
        <div class="empty-state">
            <h3>No users found</h3>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $i => $user)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:var(--ink);">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <strong>{{ $user->name }}</strong>
                            @if($user->id === auth()->id())
                                <span class="badge" style="background:#e0f2fe;color:#075985;font-size:.7rem;">You</span>
                            @endif
                        </div>
                    </td>
                    <td style="color:var(--muted);font-size:.87rem;">{{ $user->email }}</td>
                    <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
@endif

@endsection