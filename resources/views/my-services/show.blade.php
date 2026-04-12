@extends('layouts.app')

@section('content')

<div class="card">
    <div class="page-header" style="margin-bottom:0">
        <h1>Welcome, {{ Auth::user()->name }} 👋</h1>
        <p>Need something during your stay? Submit a service request and our team will take care of it.</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
        <a href="{{ route('my-services.create') }}" class="btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Service Request
        </a>
        <a href="{{ route('my-services.index') }}" class="btn btn-secondary">View My Requests</a>
    </div>
</div>

@endsection