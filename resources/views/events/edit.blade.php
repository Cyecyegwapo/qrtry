@extends('layouts.app') {{-- Assuming layouts/app.blade.php --}}

@section('content') {{-- Assuming a content section --}}
<div class="container">
    <h1>Edit Event: {{ $event->title }}</h1> {{-- Show which event is being edited --}}

    {{-- Display validation errors if any --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form points to the update route for this specific event --}}
    <form action="{{ route('events.update', $event) }}" method="POST">
        @csrf {{-- CSRF Protection --}}
        @method('PUT') {{-- Method Spoofing: Use PUT or PATCH for updates --}}

        {{-- Title Field --}}
        <div class="form-group mb-3">
            <label for="title" class="form-label">Title</label>
            {{-- Use old() first, fallback to current $event data --}}
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $event->title) }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Description Field --}}
        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $event->description) }}</textarea>
             @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Date Field --}}
        <div class="form-group mb-3">
            <label for="date" class="form-label">Date</label>
            {{-- Format date for input type="date" if needed, Carbon helps --}}
            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $event->date->format('Y-m-d')) }}" required>
             @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         {{-- Time Field --}}
         <div class="form-group mb-3">
            <label for="time" class="form-label">Time</label>
            {{-- Format time if needed, assumes H:i format --}}
            <input type="time" name="time" id="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time', $event->time) }}" required>
             @error('time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Location Field --}}
        <div class="form-group mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $event->location) }}" required>
            @error('location')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Year Level Field --}}
        <div class="form-group mb-3">
            <label for="year_level" class="form-label">Target Year Level (Optional)</label>
            <input type="text" name="year_level" id="year_level" class="form-control @error('year_level') is-invalid @enderror" value="{{ old('year_level', $event->year_level) }}">
            @error('year_level')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Department Field --}}
        <div class="form-group mb-3">
            <label for="department" class="form-label">Target Department (Optional)</label>
            <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $event->department) }}">
            @error('department')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Event</button>
        <a href="{{ route('events.show', $event) }}" class="btn btn-secondary mt-3">Cancel</a> {{-- Link back to show page --}}

    </form>
</div>
@endsection