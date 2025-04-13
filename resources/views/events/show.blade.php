@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $event->title }}</h1>
        <p><strong>Description:</strong> {{ $event->description }}</p>
        <p><strong>Date:</strong> {{ $event->date }}</p>
        <p><strong>Time:</strong> {{ $event->time }}</p>
        <p><strong>Location:</strong> {{ $event->location }}</p>
        <p><strong>Attendance Count:</strong> {{ $attendanceCount }}</p>

         @if(auth()->user()->isAdmin())
            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">Edit</a>
            <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
             <a href="{{ route('events.qrcode', $event->id) }}" class="btn btn-secondary">Generate QR Code</a>
             <a href="{{ route('events.attendance', $event->id) }}" class="btn btn-warning">View Attendance</a>
        @endif
    </div>
@endsection
