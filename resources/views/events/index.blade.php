@extends('layouts.app') {{-- Assuming layouts/app.blade.php is one layout --}}

{{-- This section uses Blade Component syntax, potentially a different layout --}}
{{-- Kept as provided, but review if both layout methods are intended --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- This heading might be redundant if the one below is used --}}
            {{-- <h1>Events List</h1> --}}
        </div>
    </div>
</x-app-layout>


{{-- This section uses Bootstrap styling and assumes the @extends('layouts.app') layout --}}
{{-- Consider consolidating layout approaches if necessary --}}
{{-- Add @section if layouts.app uses @yield('content') --}}
    <div class="container">
        <h1>Events</h1>

        {{-- Display success message --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- "Create Event" button only shown once, for admins --}}
        @if(auth()->user()->isAdmin())
            <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">Create Event</a>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover"> {{-- Added some Bootstrap table classes --}}
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            @if(auth()->user()->isAdmin()) {{-- QR Status column only for Admins --}}
                                <th>QR Status</th> {{-- ADDED: QR Status Header --}}
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event) {{-- Use forelse for empty check --}}
                            <tr>
                                <td>{{ $event->title }}</td>
                                {{-- Format date/time display --}}
                                <td>{{ optional($event->date)->format('Y-m-d') }}</td>
                                <td>{{ $event->time }}</td> {{-- Format if necessary --}}
                                <td>{{ $event->location }}</td>

                                {{-- ADDED: QR Status Column Data (Admin only) --}}
                                @if(auth()->user()->isAdmin())
                                    <td>
                                        @if ($event->qrcode)
                                            <span class="badge {{ $event->qrcode->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $event->qrcode->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            {{-- Optionally show current validity --}}
                                            {{-- <small class="d-block {{ $event->qrcode->isValidNow() ? 'text-success' : 'text-danger' }}">
                                                ({{ $event->qrcode->isValidNow() ? 'Valid Now' : 'Invalid Now' }})
                                            </small> --}}
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                @endif
                                {{-- END OF ADDED QR Status Column Data --}}

                                <td>
                                    {{-- Actions accessible to all authenticated users --}}
                                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-sm btn-info" title="View Details">View</a>

                                    {{-- Actions accessible only to admins --}}
                                     @if(auth()->user()->isAdmin())
                                         <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-primary" title="Edit Event & QR Settings">Edit</a>
                                         <a href="{{ route('events.qrcode', $event->id) }}" class="btn btn-sm btn-secondary" title="View QR Code Page">QR Page</a>
                                         <a href="{{ route('events.attendance', $event->id) }}" class="btn btn-sm btn-warning" title="View Attendance List">Attendance</a>
                                         {{-- Delete Form --}}
                                         <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                             @csrf
                                             @method('DELETE')
                                             <button type="submit" class="btn btn-sm btn-danger" title="Delete Event">Delete</button>
                                         </form>
                                     @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Adjust colspan based on whether QR Status column is visible --}}
                                <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" class="text-center">No events found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
