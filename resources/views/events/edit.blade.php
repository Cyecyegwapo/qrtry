@extends('layouts.app') {{-- Assuming layouts/app.blade.php --}}

 {{-- Added @section assuming layouts.app uses @yield('content') --}}
<div class="container">
    <h1>Edit Event: {{ $event->title }}</h1> {{-- Show which event is being edited --}}

    {{-- Display validation errors if any (Applies to both forms potentially) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- Display success messages if redirected from update --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- ==         FORM 1: EDIT EVENT DETAILS                 == --}}
    {{-- ======================================================== --}}
    {{-- Form points to the update route for this specific event --}}
    <form action="{{ route('events.update', $event->id) }}" method="POST"> {{-- Ensure $event->id if not using route model binding shortcut --}}
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
             {{-- Use optional() in case date is null initially --}}
            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', optional($event->date)->format('Y-m-d')) }}" required>
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

        <button type="submit" class="btn btn-primary mt-3">Update Event Details</button>
        {{-- Cancel button moved to the end --}}

    </form> {{-- End of Event Details Form --}}

    <hr> {{-- Add a separator --}}

    {{-- ======================================================== --}}
    {{-- ==         FORM 2: EDIT QR CODE SETTINGS              == --}}
    {{-- ======================================================== --}}
    <h2>QR Code Settings</h2>

    {{-- Check if the QR code relationship is loaded and exists --}}
    {{-- Assumes $event->load('qrcode') was called in the controller's edit method --}}
    @if ($event->qrcode)
        {{-- This form points to the dedicated QR Code settings update route --}}
        {{-- *** CORRECTED STRUCTURE: Inputs and button are NOW INSIDE the form *** --}}
        <form method="POST" action="{{ route('events.updateQrSettings', $event->id) }}">
            @csrf
            @method('PUT') {{-- ** ADDED Method Spoofing for PUT request ** --}}

            {{-- Active From Field --}}
            <div class="form-group mb-3">
                <label for="qr_active_from" class="form-label">QR Active From (Optional)</label>
                <input type="datetime-local" class="form-control @error('active_from') is-invalid @enderror"
                       id="qr_active_from" name="active_from"
                       value="{{ old('active_from', $event->qrcode->active_from?->format('Y-m-d\TH:i')) }}">
                @error('active_from')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Active Until Field --}}
            <div class="form-group mb-3">
                <label for="qr_active_until" class="form-label">QR Active Until (Optional)</label>
                <input type="datetime-local" class="form-control @error('active_until') is-invalid @enderror"
                       id="qr_active_until" name="active_until"
                       value="{{ old('active_until', $event->qrcode->active_until?->format('Y-m-d\TH:i')) }}">
                {{-- Display validation error specifically for active_until --}}
                 @error('active_until')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Is Active Checkbox --}}
            <div class="form-check mb-3">
                {{-- Hidden input ensures '0' is sent if checkbox is unchecked --}}
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox"
                       id="qr_is_active" name="is_active" value="1"
                       {{ old('is_active', $event->qrcode->is_active ?? true) ? 'checked' : '' }}> {{-- Added default true if qrcode exists but is_active is null --}}
                <label class="form-check-label" for="qr_is_active">
                    QR Code Enabled
                </label>
                 @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit button for THIS form --}}
            <button type="submit" class="btn btn-secondary mt-3">Update QR Settings</button>

        </form> {{-- *** End of QR Code Settings Form *** --}}
    @else
        {{-- Message shown if no QR code record exists for this event --}}
        <p class="text-muted">QR Code has not been generated for this event yet, or the record is missing.</p>
    @endif


    {{-- Cancel button applies to the whole page --}}
    <div class="mt-4">
        <a href="{{ route('events.show', $event->id) }}" class="btn btn-light">Cancel / Back to Event</a> {{-- Use $event->id --}}
    </div>

</div> {{-- End of container div --}}

