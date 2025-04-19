@extends('layouts.app')


    <div class="container">
        <h1>QR Code for {{ $event->title }}</h1>
        <div class="text-center">
            {!! $qrCode !!}
            <p>Scan this QR code to record your attendance for the event.</p>
             <p>
                <strong>Event:</strong> {{ $event->title }}<br>
                <strong>Date:</strong> {{ $event->date }}<br>
                <strong>Time:</strong> {{ $event->time }}
            </p>
        </div>
    </div>

