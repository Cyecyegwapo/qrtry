@extends('layouts.app')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1>Events List</h1>
            </div>
    </div>

</x-app-layout>

    <div class="container">
        <h1>Events</h1>
        <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">Create Event</a> 
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(auth()->user()->isAdmin())
            <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">Create Event</a>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->date }}</td>
                                <td>{{ $event->time }}</td>
                                <td>{{ $event->location }}</td>
                                <td>
                                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-sm btn-info">View</a>
                                     @if(auth()->user()->isAdmin())
                                        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                         <a href="{{ route('events.qrcode', $event->id) }}" class="btn btn-sm btn-secondary">Generate QR Code</a>
                                         <a href="{{ route('events.attendance', $event->id) }}" class="btn btn-sm btn-warning">View Attendance</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

