@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Event</h1>

        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ $event->title }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" required>{{ $event->description }}</textarea>
                 @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ $event->date }}" required>
                 @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
             <div class="form-group">
                <label for="time">Time</label>
                <input type="time" name="time" id="time" class="form-control @error('time') is-invalid @enderror" value="{{ $event->time }}" required>
                 @error('time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ $event->location }}" required>
                 @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
