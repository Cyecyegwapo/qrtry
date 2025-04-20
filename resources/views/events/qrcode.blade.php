@extends('layouts.app')
<div class="container">
    <h1>QR Code for: {{ $event->title }}</h1>

    <div class="text-center" style="margin-top: 20px; margin-bottom: 20px;">
        @if(!empty($qrCodeSvg))
            {{-- Display the SVG code stored in the variable --}}
            {!! $qrCodeSvg !!} {{-- Use $qrCodeSvg now --}}
            <p style="margin-top: 15px;">Scan this QR code to record your attendance.</p>
        @else
            <p class="text-danger">QR Code data is not available for this event.</p>
        @endif
    </div>

    <div style="margin-top: 30px;">
        <p>
            <strong>Event Details:</strong><br>
            Title: {{ $event->title }}<br>
            Description: {{ $event->description }}<br>
            Date: {{ $event->date->format('Y-m-d') }}<br> {{-- Format date if it's a Carbon object --}}
            Time: {{ $event->time }}<br> {{-- Consider formatting time if needed --}}
            Location: {{ $event->location }}
        </p>
        <a href="{{ route('events.show', $event) }}" class="btn btn-primary">Back to Event Details</a>
    </div>

</div>
