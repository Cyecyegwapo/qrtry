@extends('layouts.app')

{{-- ======================================================== --}}
{{-- ==          SECTION 1: EVENT DETAILS & ACTIONS        == --}}
{{-- ======================================================== --}}
@section('content') {{-- Assuming a content section in layouts.app for this part --}}
    <div class="container">
        <h1>{{ $event->title }}</h1>
        <p><strong>Description:</strong> {{ $event->description }}</p>
        {{-- Format date/time display if desired --}}
        <p><strong>Date:</strong> {{ optional($event->date)->format('Y-m-d') }}</p> {{-- Added optional() and format --}}
        <p><strong>Time:</strong> {{ $event->time }}</p> {{-- Consider formatting if $event->time is a Carbon instance --}}
        <p><strong>Location:</strong> {{ $event->location }}</p>
        <p><strong>Attendance Count:</strong> {{ $attendanceCount }}</p>
        @if($event->year_level)<p><strong>Target Year Level:</strong> {{ $event->year_level }}</p>@endif
        @if($event->department)<p><strong>Target Department:</strong> {{ $event->department }}</p>@endif

        {{-- Admin Specific Section --}}
        @if(auth()->user()->isAdmin())
            <hr> {{-- Add separator --}}
            <h4>Admin Information & Actions</h4>

            {{-- **ADDED: QR Code Status Display** --}}
            <div class="mb-3 p-3 border rounded bg-light"> {{-- Simple styling --}}
                <h5>QR Code Status:</h5>
                {{-- Check if the qrcode relationship is loaded and exists --}}
                {{-- Assumes $event->load('qrcode') was called in the controller's show method --}}
                @if ($event->qrcode)
                    <p>
                        <strong>Admin Status:</strong>
                        <span class="{{ $event->qrcode->is_active ? 'text-success' : 'text-danger' }}">
                            {{ $event->qrcode->is_active ? 'Enabled' : 'Disabled (Force Stopped)' }}
                        </span>
                    </p>
                    <p>
                        <strong>Active From:</strong>
                        {{ $event->qrcode->active_from ? $event->qrcode->active_from->format('Y-m-d H:i:s') : 'Always Active (No Start Limit)' }}
                    </p>
                    <p>
                        <strong>Active Until:</strong>
                        {{ $event->qrcode->active_until ? $event->qrcode->active_until->format('Y-m-d H:i:s') : 'Never Expires (No End Limit)' }}
                    </p>
                     <p>
                        <strong>Currently Valid for Scanning:</strong>
                        <span class="{{ $event->qrcode->isValidNow() ? 'text-success' : 'text-danger' }}">
                            {{ $event->qrcode->isValidNow() ? 'Yes' : 'No' }}
                        </span>
                         @if(!$event->qrcode->isValidNow())
                            <small class="d-block text-muted">
                                (Reason:
                                @if(!$event->qrcode->is_active) Disabled by Admin
                                @elseif($event->qrcode->active_from && now()->lt($event->qrcode->active_from)) Not started yet
                                @elseif($event->qrcode->active_until && now()->gt($event->qrcode->active_until)) Expired
                                @else Unknown @endif
                                )
                            </small>
                         @endif
                    </p>
                @else
                    <p class="text-muted">QR Code has not been generated for this event yet.</p>
                @endif
            </div>
            {{-- **END OF ADDED QR Code Status Display** --}}

            {{-- Existing Admin Action Buttons --}}
            <div class="mt-2 mb-3">
                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary btn-sm">Edit Event & QR Settings</a>
                <a href="{{ route('events.qrcode', $event->id) }}" class="btn btn-secondary btn-sm">View/Generate QR Code Page</a>
                <a href="{{ route('events.attendance', $event->id) }}" class="btn btn-info btn-sm">View Attendance List</a>
                 @if ($event->qrcode) {{-- Only show download if QR exists --}}
                     <a href="{{ route('events.qrcode.download', $event->id) }}" class="btn btn-success btn-sm">Download QR Code</a>
                 @endif
                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event and all related data?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete Event</button>
                </form>
            </div>
             <hr> {{-- Add separator --}}
        @endif
        {{-- End Admin Specific Section --}}

    </div> {{-- End of container div --}}
 {{-- End of content section --}}


{{-- ======================================================== --}}
{{-- ==   SECTION 2: QR SCANNER UI (Using Blade Components) == --}}
{{-- ======================================================== --}}
{{-- This part uses Blade component syntax and Tailwind CSS --}}
{{-- It seems separate from the Bootstrap section above --}}
{{-- Kept exactly as provided --}}

 {{-- Assuming layouts/app.blade.php uses this component syntax --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Dashboard / Scan QR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-medium mb-4">Scan Event QR Code</h3>

                    {{-- Area for Live Camera Scanner --}}
                    <div class="border dark:border-gray-700 p-4 rounded mb-4">
                        <h4 class="font-semibold mb-2">Live Camera Scan</h4>
                        {{-- 1. HTML Structure for the Live Scanner --}}
                        <div style="width: 100%; max-width: 500px; margin: auto;" id="qr-reader"></div>
                        <div class="text-center mt-4">
                             <button id="start-scan-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                 Start Camera Scan
                             </button>
                             <button id="stop-scan-btn" style="display: none;" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                 Stop Camera Scan
                             </button>
                        </div>
                    </div>

                    {{-- Area for File Upload Scanner --}}
                    <div class="border dark:border-gray-700 p-4 rounded">
                         <h4 class="font-semibold mb-2">Scan from Uploaded Image</h4>
                         {{-- ADDED: File Input --}}
                         <div>
                             <label for="qr-input-file" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Upload QR Code Image:</label>
                             <input type="file" id="qr-input-file" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                 file:mr-4 file:py-2 file:px-4
                                 file:rounded-md file:border-0
                                 file:text-sm file:font-semibold
                                 file:bg-indigo-100 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300
                                 hover:file:bg-indigo-200 dark:hover:file:bg-indigo-800 cursor-pointer"/>
                         </div>
                    </div>

                    {{-- Area for Scan Results --}}
                    <div id="qr-reader-results" class="mt-4 text-center font-medium"></div>

                </div>
            </div>
        </div>
    </div>

    {{-- Include the html5-qrcode library --}}
    @push('scripts') {{-- Push script to a stack defined in your main layout --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    {{-- JavaScript to Initialize and Control the Scanner --}}
    {{-- Kept exactly as provided --}}
    <script>
         // --- Common Variables and Functions ---
         const resultsElement = document.getElementById('qr-reader-results');
         // Instantiate the scanner class *once* globally within the script scope
         const html5QrCode = new Html5Qrcode("qr-reader"); // Target the div#qr-reader for camera

         // Function called when a QR code is successfully scanned (used by both methods)
         function onScanSuccess(decodedText, decodedResult) {
             console.log(`Code matched = ${decodedText}`, decodedResult);
             resultsElement.textContent = `Scanned: ${decodedText}`;
             resultsElement.classList.remove('text-yellow-600', 'dark:text-yellow-400', 'text-red-600', 'dark:text-red-400');
             resultsElement.classList.add('text-green-600', 'dark:text-green-400');

             // **IMPORTANT ACTION:** Redirect the user to the scanned URL
             // Simple check if it looks like an attendance URL for *this* app
             if (decodedText.includes('/events/') && decodedText.includes('/attendance/record')) {
                  stopScanning(); // Stop camera if running
                  resultsElement.textContent = `Attendance code found! Redirecting...`;
                  // Redirect the browser
                  window.location.href = decodedText;
             } else {
                  resultsElement.textContent = `Scanned non-attendance code: ${decodedText}`;
                  resultsElement.classList.remove('text-green-600', 'dark:text-green-400');
                  resultsElement.classList.add('text-yellow-600', 'dark:text-yellow-400');
                  // Optionally stop camera scanning here too if you only want one scan
                  // stopScanning();
             }
         }

         // Function called if scanning fails (used by both methods)
         function onScanFailure(error) {
             // Don't display continuous errors from camera scan failure
             // console.warn(`Code scan error = ${error}`);
             // For file scan errors, we might want to display them:
             const fileInputElement = document.getElementById('qr-input-file'); // Get file input element reference
             if (error instanceof Error && fileInputElement && fileInputElement.files.length > 0) {
                 // Check if the error object exists and if it's related to a file scan attempt
                  resultsElement.textContent = `Error scanning file: ${error.message || error}`;
                  resultsElement.classList.remove('text-green-600', 'dark:text-green-400', 'text-yellow-600', 'dark:text-yellow-400');
                  resultsElement.classList.add('text-red-600', 'dark:text-red-400');
             } else if (typeof error === 'string' && error.toLowerCase().includes('no qr code found')) {
                  resultsElement.textContent = `No QR code found in the uploaded file.`;
                  resultsElement.classList.remove('text-green-600', 'dark:text-green-400', 'text-yellow-600', 'dark:text-yellow-400');
                  resultsElement.classList.add('text-red-600', 'dark:text-red-400');
             }
         }

         // --- Live Camera Scanning Logic ---
         let isScanning = false;
         const startBtn = document.getElementById('start-scan-btn');
         const stopBtn = document.getElementById('stop-scan-btn');

         function startScanning() {
             if (isScanning) return;
             clearResults();

             // Check for camera permissions and availability before creating
             Html5Qrcode.getCameras().then(devices => {
                 if (devices && devices.length) {
                      const config = { fps: 10, qrbox: { width: 250, height: 250 }, rememberLastUsedCamera: true };
                     // Start scanning. Request camera permission. Use back camera if available.
                     html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
                          .then(() => {
                              isScanning = true;
                              startBtn.style.display = 'none';
                              stopBtn.style.display = 'inline-flex';
                              resultsElement.textContent = 'Scanning... Point camera at QR code.';
                          })
                          .catch(err => handleCameraError(err));
                 } else {
                      resultsElement.textContent = 'No cameras found on this device.';
                      resultsElement.classList.add('text-red-600', 'dark:text-red-400');
                 }
             }).catch(err => handleCameraError(err));
         }

         function stopScanning() {
            // Check html5QrCode state directly if possible and if scanning is tracked
             if (!isScanning || !html5QrCode || typeof html5QrCode.getState === 'function' && html5QrCode.getState() !== Html5QrcodeScannerState.SCANNING) {
                 // If getState exists and confirms not scanning, or if our flag is false, exit
                 // Note: getState() might not be available depending on library version/usage, fallback to isScanning flag
                 // console.log("Stop ignored: Not scanning or library state unknown.");
                 // return;
             }
             // Attempt to stop only if we think we are scanning
             if (isScanning) {
                 html5QrCode.stop().then((ignore) => {
                     console.log("QR Code scanning stopped.");
                     isScanning = false;
                     startBtn.style.display = 'inline-flex';
                     stopBtn.style.display = 'none';
                     document.getElementById('qr-reader').innerHTML = ''; // Clear video feed area
                 }).catch((err) => {
                     console.error("Failed to stop scanner:", err);
                     // Force UI state update even on error
                     isScanning = false;
                     startBtn.style.display = 'inline-flex';
                     stopBtn.style.display = 'none';
                 });
            } else {
                 // If isScanning flag was already false, just ensure UI is correct
                 startBtn.style.display = 'inline-flex';
                 stopBtn.style.display = 'none';
            }
         }


         function handleCameraError(err) {
             console.error("Camera Error:", err);
             resultsElement.textContent = `Error starting camera: ${err.message || err}. Check permissions or try uploading an image.`;
             resultsElement.classList.add('text-red-600', 'dark:text-red-400');
             // Ensure UI reflects stopped state
             isScanning = false;
             startBtn.style.display = 'inline-flex';
             stopBtn.style.display = 'none';
         }

         // --- File Upload Scanning Logic ---
         const fileInput = document.getElementById('qr-input-file');

         fileInput.addEventListener('change', e => {
             if (e.target.files && e.target.files.length > 0) {
                 const file = e.target.files[0];
                 clearResults();
                 resultsElement.textContent = 'Scanning uploaded file...';
                 stopScanning(); // Stop camera if it was running

                 // Use the same html5QrCode instance to scan the file
                 // The second argument 'true' means show the image in the qr-reader div (optional)
                 html5QrCode.scanFile(file, false) // Set to false to not show image preview in camera area
                     .then(onScanSuccess) // Reuse the success callback
                     .catch(err => {
                         // Handle errors specifically for file scanning
                         console.error(`Error scanning file. Reason: ${err}`)
                         onScanFailure(err); // Reuse the failure callback, which now handles file errors
                     });
             }
         });

         // --- Utility Functions ---
         function clearResults() {
              resultsElement.textContent = '';
              resultsElement.classList.remove('text-green-600', 'dark:text-green-400', 'text-yellow-600', 'dark:text-yellow-400', 'text-red-600', 'dark:text-red-400');
         }

         // Add event listeners to camera buttons
         startBtn.addEventListener('click', startScanning);
         stopBtn.addEventListener('click', stopScanning);

         // Optional: Clean up camera scanner when the page is unloaded
         window.addEventListener('beforeunload', () => {
             // Check isScanning flag before attempting to stop
             if (isScanning) {
                // Attempt graceful stop, ignore errors as page is unloading anyway
                try {
                    html5QrCode.stop();
                } catch (e) {
                    console.warn("Error stopping scanner during unload:", e);
                }
             }
         });

    </script>
    @endpush

