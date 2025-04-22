

@section('title', 'QR Code for ' . $event->title) {{-- Optional: Set page title --}}

@section('content') {{-- Start the content section --}}
<div class="container">
    <h1>QR Code for: {{ $event->title }}</h1>

    {{-- QR Code Display Area --}}
    <div class="text-center my-4"> {{-- Added my-4 for vertical margin --}}
        @if(!empty($qrCodeSvg))
            {{-- Display the SVG code --}}
            <div class="d-inline-block border p-3 bg-white"> {{-- Added border/padding --}}
                {!! $qrCodeSvg !!}
            </div>
            <p class="mt-3">Scan this QR code to record your attendance.</p> {{-- Moved <p> outside the centered div for better flow --}}

            {{-- === DOWNLOAD BUTTON ADDED HERE === --}}
            <div class="mt-3">
                 <a href="{{ route('events.qrcode.download', $event) }}" class="btn btn-success">
                     Download QR Code (SVG)
                 </a>
            </div>
            {{-- === END DOWNLOAD BUTTON === --}}

        @else
            <p class="alert alert-danger">QR Code data is not available for this event.</p>
        @endif
    </div>

    {{-- Event Details Summary --}}
    <div class="mt-4 p-3 border rounded bg-light"> {{-- Light background for details --}}
        <h5 class="mb-3">Event Details Summary:</h5>
        <p><strong>Title:</strong> {{ $event->title }}</p>
        {{-- <p><strong>Description:</strong> {{ $event->description }}</p> --}} {{-- Optional: Keep it brief here --}}
        <p><strong>Date:</strong> {{ $event->date ? $event->date->format('M d, Y') : 'N/A' }}</p>
        <p><strong>Time:</strong> {{ $event->time ? date('h:i A', strtotime($event->time)) : 'N/A' }}</p>
        <p><strong>Location:</strong> {{ $event->location ?? 'N/A' }}</p>
    </div>

    {{-- Back Button --}}
    <div class="mt-4">
        <a href="{{ route('events.show', $event) }}" class="btn btn-primary">Back to Event Details</a>
    </div>

</div> {{-- End .container --}}
