@extends('layouts.app') {{-- Assuming layouts/app.blade.php --}}

 {{-- Assuming a content section --}}
<div class="container">
    <h1>Create New Event</h1>

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

    <form action="{{ route('events.store') }}" method="POST">
        @csrf {{-- CSRF Protection --}}

        {{-- Title Field --}}
        <div class="form-group mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Description Field --}}
        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea> {{-- Removed required to match nullable validation --}}
             @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Date Field --}}
        <div class="form-group mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
             @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         {{-- Time Field --}}
         <div class="form-group mb-3">
            <label for="time" class="form-label">Time</label>
            <input type="time" name="time" id="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time') }}" required>
             @error('time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Location Field --}}
        <div class="form-group mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" required>
            @error('location')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Year Level Field --}}
        <div class="form-group mb-3">
            <label for="year_level" class="form-label">Target Year Level (Optional)</label>
            {{-- Consider changing type="text" to a <select> dropdown later --}}
            <input type="text" name="year_level" id="year_level" class="form-control @error('year_level') is-invalid @enderror" value="{{ old('year_level') }}">
            @error('year_level')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Department Field --}}
        <div class="form-group mb-3">
            <label for="department" class="form-label">Target Department (Optional)</label>
             {{-- Consider changing type="text" to a <select> dropdown later --}}
            <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}">
            @error('department')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Event</button>
        <a href="{{ route('events.index') }}" class="btn btn-secondary mt-3">Cancel</a>

    </form>
</div>
