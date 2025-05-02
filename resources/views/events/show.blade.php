{{-- Use the component layout ONLY --}}
<x-app-layout>
    {{-- Define the header content --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- Header content adapted based on role --}}
            @if(auth()->user()?->isAdmin())
                {{ __('Event Details & Admin: ') }} {{ $event->title }}
            @else
                {{ isset($event) ? __('Scan QR for: ') . $event->title : __('Scan Event QR Code') }}
            @endif
        </h2>
    </x-slot>

    {{-- Main Content Area --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Conditional Display: Show Admin View OR User Scanner View --}}
            @if(auth()->user()?->isAdmin())

                {{-- ======================================================== --}}
                {{-- == SECTION 1: ADMIN VIEW (DETAILS & ACTIONS)         == --}}
                {{-- ======================================================== --}}
                <div class="space-y-6 max-w-4xl mx-auto"> {{-- Max width for admin content --}}
                    {{-- Event Details Card --}}
                    <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                         <div class="p-6 sm:p-8 border-b border-gray-200 dark:border-gray-700">
                             <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-5">Event Information</h3>
                             <dl class="text-sm">
                                 {{-- Title --}}
                                 <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Title:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100">{{ $event->title }}</dd>
                                 </div>
                                 {{-- Description --}}
                                 <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Description:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $event->description }}</dd>
                                 </div>
                                 {{-- Date --}}
                                 <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Date:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100">{{ optional($event->date)->format('F j, Y') }}</dd>
                                 </div>
                                 {{-- Time --}}
                                 <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Time:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100">{{ $event->time ? \Carbon\Carbon::parse($event->time)->format('g:i A') : 'N/A' }}</dd>
                                 </div>
                                 {{-- Location --}}
                                 <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Location:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100">{{ $event->location }}</dd>
                                 </div>
                                 {{-- Attendance Count --}}
                                 <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Attendance Count:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100">{{ $attendanceCount ?? '0' }}</dd>
                                 </div>
                                 {{-- Optional Fields --}}
                                 @if($event->year_level)
                                 <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Target Year Level:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100">{{ $event->year_level }}</dd>
                                 </div>
                                 @endif
                                 @if($event->department)
                                 <div class="grid grid-cols-3 gap-4 py-2 {{ !$event->year_level ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
                                     <dt class="col-span-1 font-semibold text-gray-700 dark:text-gray-300">Target Department:</dt>
                                     <dd class="col-span-2 text-gray-900 dark:text-gray-100">{{ $event->department }}</dd>
                                 </div>
                                 @endif
                             </dl>
                         </div>
                    </div>

                    {{-- Admin Specific Section Card --}}
                    <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                        <div class="p-6 sm:p-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-5 border-b border-gray-200 dark:border-gray-700 pb-3">Admin Information & Actions</h3>

                            {{-- QR Code Status Display (Tailwind Styled) --}}
                            <div class="mb-6 p-4 border border-stone-300 dark:border-stone-700 rounded-md bg-stone-50 dark:bg-stone-800/50">
                                 <h4 class="font-semibold mb-3 text-gray-800 dark:text-gray-200">QR Code Status</h4>
                                 @if ($event->qrcode)
                                    <dl class="text-sm">
                                         {{-- Admin Status Row --}}
                                         <div class="grid grid-cols-3 gap-2 py-1">
                                             <dt class="col-span-1 font-medium text-gray-600 dark:text-gray-400">Admin Status:</dt>
                                             <dd class="col-span-2">
                                                 <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $event->qrcode->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                     {{ $event->qrcode->is_active ? 'Enabled' : 'Disabled' }}
                                                 </span>
                                             </dd>
                                         </div>
                                         {{-- Active From Row --}}
                                         <div class="grid grid-cols-3 gap-2 py-1">
                                             <dt class="col-span-1 font-medium text-gray-600 dark:text-gray-400">Active From:</dt>
                                             <dd class="col-span-2 text-gray-800 dark:text-gray-200">{{ $event->qrcode->active_from ? $event->qrcode->active_from->format('Y-m-d g:i A') : 'Always Active' }}</dd>
                                         </div>
                                         {{-- Active Until Row --}}
                                         <div class="grid grid-cols-3 gap-2 py-1">
                                             <dt class="col-span-1 font-medium text-gray-600 dark:text-gray-400">Active Until:</dt>
                                             <dd class="col-span-2 text-gray-800 dark:text-gray-200">{{ $event->qrcode->active_until ? $event->qrcode->active_until->format('Y-m-d g:i A') : 'Never Expires' }}</dd>
                                         </div>
                                         {{-- Current Validity Row --}}
                                         <div class="grid grid-cols-3 gap-2 py-1 items-start">
                                             <dt class="col-span-1 font-medium text-gray-600 dark:text-gray-400">Currently Valid:</dt>
                                             <dd class="col-span-2">
                                                  <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $event->qrcode->isValidNow() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                      {{ $event->qrcode->isValidNow() ? 'Yes' : 'No' }}
                                                  </span>
                                                  {{-- Reason if not valid --}}
                                                  @if(!$event->qrcode->isValidNow())
                                                     <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                         (Reason:
                                                         @if(!$event->qrcode->is_active) Admin Disabled
                                                         @elseif($event->qrcode->active_from && now()->lt($event->qrcode->active_from)) Not started yet
                                                         @elseif($event->qrcode->active_until && now()->gt($event->qrcode->active_until)) Expired
                                                         @else Other reason @endif)
                                                     </span>
                                                @endif
                                             </dd>
                                         </div>
                                    </dl>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">QR Code details not available (generate via Edit page).</p>
                                @endif
                            </div>

                            {{-- Admin Action Buttons (Tailwind Styled) --}}
                            <h4 class="font-semibold mb-3 text-gray-800 dark:text-gray-200">Actions</h4>
                            <div class="flex flex-wrap gap-3">
                                {{-- Buttons using specific colors --}}
                                <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="Edit Event & QR Settings">Edit</a>
                                <a href="{{ route('events.qrcode', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="View QR Code Page">QR Page</a>
                                <a href="{{ route('events.attendance', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="View Attendance List">Attendance List</a>
                                @if ($event->qrcode)
                                    <a href="{{ route('events.qrcode.download', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="Download QR Code">Download QR</a>
                                @endif
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event and all related data?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="Delete Event">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @else

                {{-- ======================================================== --}}
                {{-- == SECTION 2: USER VIEW (QR SCANNER UI ONLY)         == --}}
                {{-- ======================================================== --}}
                 {{-- This section contains the QR Scanner UI, styled with Tailwind --}}
                <div class="space-y-6 max-w-xl mx-auto"> {{-- Max width for scanner --}}
                     <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                         <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">
                             <h3 class="text-lg font-semibold mb-5 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-3">
                                Scan QR Code for: {{ $event->title }}
                             </h3>
                             {{-- Area for Live Camera Scanner --}}
                             <div class="border dark:border-gray-600 p-4 rounded-lg mb-6 bg-gray-50 dark:bg-gray-800/50">
                                 <h4 class="font-semibold mb-3 text-center text-gray-800 dark:text-gray-200">Live Camera Scan</h4>
                                 <div class="w-full max-w-xs sm:max-w-sm mx-auto aspect-square bg-black rounded-md flex items-center justify-center mb-4 overflow-hidden" id="qr-reader">
                                     <p class="text-gray-400 dark:text-gray-500 text-sm">Initializing Camera...</p>
                                 </div>
                                 <div class="text-center space-x-3">
                                     <button id="start-scan-btn" class="inline-flex items-center px-5 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">Start Scan</button>
                                     <button id="stop-scan-btn" style="display: none;" class="inline-flex items-center px-5 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700 focus:bg-rose-700 active:bg-rose-900 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Stop Scan</button>
                                 </div>
                             </div>
                             {{-- Area for File Upload Scanner --}}
                             <div class="border dark:border-gray-600 p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                                  <h4 class="font-semibold mb-3 text-center text-gray-800 dark:text-gray-200">Scan from Uploaded Image</h4>
                                  <div>
                                      <label for="qr-input-file" class="sr-only">Upload QR Code Image:</label>
                                      <input type="file" id="qr-input-file" accept="image/*" class="block w-full text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-100 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:cursor-pointer file:bg-indigo-200 dark:file:bg-indigo-700 file:text-indigo-800 dark:file:text-indigo-100 hover:file:bg-indigo-300 dark:hover:file:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"/>
                                  </div>
                             </div>
                             {{-- Area for Scan Results --}}
                             <div id="qr-reader-results" class="mt-6 text-center font-medium py-3 px-4 rounded-lg min-h-[50px] text-sm">
                                {{-- Results will be injected here by JS --}}
                             </div>
                         </div>
                     </div>
                </div>
            @endif

        </div> {{-- End Max Width Container --}}
    </div> {{-- End Outer Padding Div --}}


    {{-- Push the Scanner JavaScript to the layout's script stack --}}
    {{-- Only include JS if the user is NOT an admin (they see the scanner) --}}
    @if(!auth()->user()?->isAdmin())
        {{-- Prepare event data for JS --}}
        @php
            $eventStartDateTime = null;
            // Ensure both date and time are set before trying to combine
            if ($event->date && $event->time) {
                try {
                    $eventDate = ($event->date instanceof \Carbon\Carbon) ? $event->date : \Carbon\Carbon::parse($event->date);
                    $timeParts = \Carbon\Carbon::parse($event->time);
                    $combinedDateTime = $eventDate->copy()->setTime($timeParts->hour, $timeParts->minute, $timeParts->second);
                    $eventStartDateTime = $combinedDateTime->toISOString();
                } catch (\Exception $e) {
                    // Use Illuminate\Support\Facades\Log; if not already imported
                    \Log::error("Error parsing event date/time for QR validation: Event ID {$event->id}", ['exception' => $e]);
                    $eventStartDateTime = null;
                }
            }

            // Get QR code active status (default to false if QR code relationship doesn't exist or is_active is false)
            $isQrCodeActive = $event->qrcode ? $event->qrcode->is_active : false;

        @endphp

        @push('scripts')
            {{-- Include the html5-qrcode library --}}
            <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

            <script>
                // Pass PHP variables safely to JS
                const currentPageEventId = @json($event->id);
                const appBaseUrl = @json(rtrim(url('/'), '/'));
                const eventStartDateTimeString = @json($eventStartDateTime); // ISO string or null
                const isQrCodeActive = @json($isQrCodeActive); // Boolean (true/false)

                // Log variables for debugging
                console.log('Base URL:', appBaseUrl);
                console.log('Current Page Event ID:', currentPageEventId);
                console.log('Event Start DateTime String:', eventStartDateTimeString);
                console.log('QR Code Active Status:', isQrCodeActive);

                // --- DOM Elements ---
                const resultsElement = document.getElementById('qr-reader-results');
                const qrReaderDiv = document.getElementById('qr-reader');
                const fileInput = document.getElementById('qr-input-file');
                const startBtn = document.getElementById('start-scan-btn');
                const stopBtn = document.getElementById('stop-scan-btn');

                // --- Scanner State ---
                let html5QrCode = null; // Instance of the scanner library
                let isScanning = false; // Track if the camera is currently active

                /**
                 * Initializes the Html5Qrcode scanner instance.
                 */
                function initializeQrCodeScanner() {
                    // Prevent re-initialization or initialization if the target div isn't found
                    if (html5QrCode || !qrReaderDiv) return;
                    try {
                        html5QrCode = new Html5Qrcode("qr-reader");
                        console.log("Html5Qrcode scanner initialized successfully.");
                    } catch (e) {
                        console.error("Html5Qrcode initialization failed:", e);
                        // Disable scanning UI elements if initialization fails
                        if(startBtn) startBtn.disabled = true;
                        if(fileInput) fileInput.disabled = true;
                        qrReaderDiv.innerHTML = '<p class="text-red-500 p-4">QR Scanner failed to load.</p>';
                        updateResult("QR Scanner failed to load. Please refresh the page or contact support.", "error");
                    }
                }

                /**
                 * Updates the results display area with a message and status type.
                 * @param {string} message The message to display.
                 * @param {'info'|'success'|'error'|'warning'|'clear'|'default'} [type='info'] The type of message (controls styling).
                 */
                function updateResult(message, type = 'info') {
                     if (!resultsElement) return; // Exit if results element not found
                     resultsElement.textContent = message;
                     // Define base classes (consistent padding, alignment, etc.)
                     const baseClasses = ['mt-6', 'text-center', 'font-medium', 'py-3', 'px-4', 'rounded-lg', 'min-h-[50px]', 'text-sm'];
                     resultsElement.className = baseClasses.join(' '); // Reset to base classes

                     // Define Tailwind classes for each message type
                     const typeClasses = {
                         success: 'bg-green-100/50 dark:bg-green-900/50 text-green-700 dark:text-green-200',
                         error:   'bg-red-100/50 dark:bg-red-900/50 text-red-700 dark:text-red-200',
                         warning: 'bg-yellow-100/50 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-200',
                         info:    'bg-blue-100/50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-200',
                         default: 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'
                     };

                     // Apply specific type classes, or default if type is unknown or 'clear'
                     if (type !== 'clear' && typeClasses[type]) {
                         resultsElement.classList.add(...typeClasses[type].split(' '));
                     } else if (type !== 'clear') {
                         resultsElement.classList.add(...typeClasses['default'].split(' '));
                     }
                     // Clear message content specifically for 'clear' type
                     if (type === 'clear') {
                         resultsElement.textContent = '';
                     }
                }

                /**
                 * Callback function executed when a QR code is successfully scanned (by camera or file).
                 * Performs all validation checks before redirecting.
                 * @param {string} decodedText The text decoded from the QR code (expected to be a URL).
                 * @param {object} decodedResult Additional details from the scanner library.
                 */
                function onScanSuccess(decodedText, decodedResult) {
                    console.log(`Scan Success Candidate: Code = ${decodedText}`, decodedResult);
                    stopScanning(); // Stop camera scanning after a successful read

                    // --- Validation Chain ---

                    // 1. QR Code Active Check: Is the QR code enabled in the admin settings?
                    if (isQrCodeActive === false) {
                        console.log("Validation Failed (Step 1): QR Code is disabled.");
                        updateResult('Invalid QR: This QR code is currently disabled by the administrator.', 'error');
                        return; // Halt validation
                    }
                    console.log("Validation Passed (Step 1): QR Code is active.");

                    // 2. Format & Origin Check: Does the URL match the expected pattern and base URL?
                    const pathPattern = /\/events\/(\d+)\/attendance\/record$/;
                    const match = decodedText.match(pathPattern);

                    if (!match || !match[1] || !appBaseUrl || !decodedText.startsWith(appBaseUrl)) {
                        console.log("Validation Failed (Step 2): URL format or origin mismatch.", { decodedText, match, appBaseUrl, startsWithCheck: appBaseUrl ? decodedText.startsWith(appBaseUrl) : 'appBaseUrl_missing' });
                        updateResult(`Invalid QR: Code does not match the expected format or origin.`, 'error');
                        return; // Halt validation
                    }
                    const scannedEventId = parseInt(match[1], 10);
                    console.log(`Validation Passed (Step 2): URL format and origin. Scanned Event ID: ${scannedEventId}`);

                    // 3. Event Start Time Check: Has the event officially started? (Skip if no start time is set)
                    if (eventStartDateTimeString) {
                        try {
                            const eventStartTime = new Date(eventStartDateTimeString);
                            const now = new Date();
                            if (now < eventStartTime) {
                                console.log(`Validation Failed (Step 3): Event Start Time. Current time (${now.toISOString()}) is before event start (${eventStartTime.toISOString()})`);
                                updateResult(`Invalid QR: This event has not started yet. Scanning begins at ${eventStartTime.toLocaleString()}.`, 'error');
                                return; // Halt validation
                            }
                             console.log(`Validation Passed (Step 3): Event Start Time. Current time (${now.toISOString()}) is on or after event start (${eventStartTime.toISOString()})`);
                        } catch (e) {
                            console.error("JavaScript Error: Failed to parse event start date/time string:", eventStartDateTimeString, e);
                            updateResult('Internal Error: Could not verify event start time. Please contact support.', 'error');
                            return; // Halt validation due to parsing error
                        }
                    } else {
                         console.log("Validation Skipped (Step 3): Event start time is not set for this event.");
                    }

                    // 4. Event ID Match Check: Does the event ID in the QR code match this page's event ID?
                    if (scannedEventId !== currentPageEventId) {
                        console.log(`Validation Failed (Step 4): Event ID mismatch. Scanned: ${scannedEventId}, Expected: ${currentPageEventId}`);
                        updateResult(`Invalid QR: This code is for a different event (Expected: ${currentPageEventId}, Scanned: ${scannedEventId}).`, 'error');
                        return; // Halt validation
                    }
                    console.log("Validation Passed (Step 4): Event ID Match.");

                    // --- All Validations Passed ---
                    console.log("All validations successful. Preparing to redirect...");
                    updateResult('Valid attendance code found! Redirecting now...', 'success');
                    // Redirect to the validated attendance recording URL
                    setTimeout(() => {
                        window.location.href = decodedText;
                    }, 500); // Brief delay so user sees the success message

                }

                /**
                 * Callback function executed when the scanner fails to decode a QR code.
                 * Used primarily for file scan errors, less so for continuous camera scanning.
                 * @param {Error|string} error The error object or message from the library.
                 */
                function onScanFailure(error) {
                     // Show specific errors for file scans, as they are user-initiated actions
                     if (fileInput && fileInput.files && fileInput.files.length > 0) {
                         let errorMessage = "Could not scan QR code from the selected file.";
                         if (error instanceof Error) {
                             errorMessage = `Error scanning file: ${error.message || 'Unknown error'}`;
                         } else if (typeof error === 'string') {
                             // Try to provide a more user-friendly message for common library errors
                             errorMessage = error.toLowerCase().includes('no qr code found')
                               ? `No QR code was found in the image.`
                               : `Error scanning file: ${error}`;
                         }
                         console.error("File Scan Failure:", error);
                         updateResult(errorMessage, 'error');
                     } else {
                         // Ignore continuous "No QR code found" messages from the camera stream to avoid spamming logs/UI
                         if (!(typeof error === 'string' && error.toLowerCase().includes('no qr code found'))) {
                             console.warn(`Camera Scan Issue (non-blocking): ${error}`);
                             // Avoid updating the main result display for minor camera issues
                         }
                     }
                 }


                /**
                 * Starts the camera scanning process.
                 */
                function startScanning() {
                    if (isScanning || !html5QrCode) {
                         console.log("Scan start prevented: Already scanning or scanner not initialized.");
                         return;
                    }
                    clearResults(); // Clear any previous results
                    qrReaderDiv.innerHTML = ''; // Clear placeholder text/video
                    updateResult('Initializing camera...', 'info');
                    qrReaderDiv.classList.remove('items-center', 'justify-center'); // Allow video element to fill space

                    Html5Qrcode.getCameras().then(devices => {
                        if (!devices || !devices.length) {
                           throw new Error('No cameras found on this device.'); // Throw error to be caught by handleCameraError
                        }
                        // Prefer back camera if available based on label heuristics
                        const preferredCamera = devices.find(d => d.label.toLowerCase().includes('back'));
                        const cameraId = preferredCamera ? preferredCamera.id : devices[0].id; // Fallback to the first camera
                        console.log(`Attempting to use camera: ${cameraId} (${preferredCamera?.label || devices[0].label})`);

                        // Configuration for the scanner
                        const config = {
                            fps: 10, // Target frames per second for scanning
                            qrbox: (viewfinderWidth, viewfinderHeight) => { // Dynamic QR box sizing
                                let minEdgePercentage = 0.7;
                                let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                                let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                                // Clamp the size to reasonable limits
                                qrboxSize = Math.max(100, Math.min(qrboxSize, viewfinderWidth, viewfinderHeight));
                                return { width: qrboxSize, height: qrboxSize };
                            },
                            rememberLastUsedCamera: false, // Don't persist camera choice across sessions
                            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA], // Explicitly use camera scan type
                            aspectRatio: 1.0 // Attempt square aspect ratio
                        };

                        // Start the scanner
                        return html5QrCode.start(cameraId, config, onScanSuccess, onScanFailure);

                    }).then(() => {
                        // This .then() executes after html5QrCode.start resolves successfully
                        console.log("Camera scanner started successfully.");
                        isScanning = true;
                        if(startBtn) startBtn.style.display = 'none'; // Hide Start button
                        if(stopBtn) stopBtn.style.display = 'inline-flex'; // Show Stop button
                        updateResult('Scanning... Point camera at QR code.', 'info'); // Update status message
                    }).catch(handleCameraError); // Catch errors from getCameras() or html5QrCode.start()
                }

                /**
                 * Stops the active camera scanning process.
                 */
                function stopScanning() {
                    // Exit if scanner isn't active or initialized
                    if (!html5QrCode || !isScanning) {
                        console.log("Stop requested but scanner not active or not initialized.");
                        isScanning = false; // Ensure state is correct
                        // Update UI just in case it's inconsistent
                        if(startBtn) startBtn.style.display = 'inline-flex';
                        if(stopBtn) stopBtn.style.display = 'none';
                        // Restore placeholder if needed
                        if (qrReaderDiv && qrReaderDiv.innerHTML.trim() === '') {
                            qrReaderDiv.innerHTML = '<p class="text-gray-400 dark:text-gray-500 text-sm p-4">Camera view stopped.</p>';
                            qrReaderDiv.classList.add('items-center', 'justify-center');
                        }
                        return;
                    }

                    console.log("Attempting to stop camera scanner...");
                    try {
                        // html5QrCode.stop() returns a Promise
                        html5QrCode.stop()
                            .then(() => {
                                console.log("Camera scanner stopped successfully (Promise resolved).");
                                // UI updates are handled in the finally block
                            })
                            .catch(err => {
                                console.error("Error occurred while stopping camera scanner (Promise rejected):", err);
                                // UI updates will still happen in finally
                            })
                            .finally(() => {
                                console.log("Executing finally block after stop attempt.");
                                isScanning = false; // Update state regardless of success/error
                                // Update button visibility
                                if(startBtn) startBtn.style.display = 'inline-flex';
                                if(stopBtn) stopBtn.style.display = 'none';
                                // Reset the QR reader div content
                                if(qrReaderDiv) {
                                    qrReaderDiv.innerHTML = '<p class="text-gray-400 dark:text-gray-500 text-sm p-4">Camera view stopped.</p>';
                                    qrReaderDiv.classList.add('items-center', 'justify-center');
                                }
                                // Update the results message if it was showing a scanning/initializing state
                                if(resultsElement && (resultsElement.textContent.startsWith('Scanning...') || resultsElement.textContent.startsWith('Initializing...'))) {
                                   updateResult('Scanner stopped.', 'info');
                                }
                            });
                    } catch (syncError) {
                        // Catch potential synchronous errors from calling .stop() itself
                        console.error("Synchronous error calling html5QrCode.stop():", syncError);
                        // Force state update and UI reset in case the promise/finally doesn't run
                        isScanning = false;
                        if(startBtn) startBtn.style.display = 'inline-flex';
                        if(stopBtn) stopBtn.style.display = 'none';
                        if(qrReaderDiv) { // Ensure UI reset happens
                           qrReaderDiv.innerHTML = '<p class="text-gray-400 dark:text-gray-500 text-sm p-4">Camera view stopped (error).</p>';
                           qrReaderDiv.classList.add('items-center', 'justify-center');
                        }
                    }
                }

                /**
                 * Handles critical errors during camera initialization or starting.
                 * @param {Error|string} err The error object or message.
                 */
                function handleCameraError(err) {
                     console.error("Camera Start/Initialization Error:", err);
                     let userMessage = 'Camera Error: ';
                     // Provide more specific feedback based on common error types
                     if (err instanceof Error) {
                         switch (err.name) {
                             case 'NotAllowedError': userMessage += 'Permission denied. Please allow camera access in browser settings.'; break;
                             case 'NotFoundError': userMessage += 'Camera not found or unavailable.'; break;
                             case 'NotReadableError': userMessage += 'Camera is already in use or a hardware error occurred.'; break;
                             case 'OverconstrainedError': userMessage += 'Camera does not meet requested constraints.'; break;
                             case 'TypeError': userMessage += 'Invalid configuration or device issue.'; break;
                             default: userMessage += err.message || 'Could not start the camera.';
                         }
                     } else if (typeof err === 'string') {
                         userMessage += err; // Use the string error directly
                     } else {
                         userMessage += 'An unknown error occurred.';
                     }

                     updateResult(userMessage, 'error'); // Show error in results area
                     isScanning = false; // Ensure scanning state is false
                     // Update button states
                     if(startBtn) { startBtn.style.display = 'inline-flex'; startBtn.disabled = false; } // Re-enable Start button
                     if(stopBtn) stopBtn.style.display = 'none';
                     // Update the QR reader div itself to show the error
                     if(qrReaderDiv) {
                         qrReaderDiv.innerHTML = `<p class="text-red-500 dark:text-red-300 p-4 text-sm font-medium">${userMessage}</p>`;
                         qrReaderDiv.classList.add('items-center', 'justify-center');
                     }
                 }

                /**
                 * Attaches event listener to the file input for scanning uploaded images.
                 */
                function setupFileInputListener() {
                    if (!fileInput) {
                        console.warn("File input element 'qr-input-file' not found. File scanning disabled.");
                        return;
                    }
                    fileInput.addEventListener('change', (e) => {
                        // Ensure scanner is initialized
                        if (!html5QrCode) {
                            initializeQrCodeScanner();
                            if (!html5QrCode) { // Check again if initialization failed
                                updateResult("Scanner is not ready. Cannot scan file.", 'error');
                                e.target.value = null; // Reset file input
                                return;
                            }
                        }

                        // Check if a file was actually selected
                        if (e.target.files && e.target.files.length > 0) {
                            const file = e.target.files[0];
                            console.log(`File selected: ${file.name}`);
                            // Stop camera scanning if it's active
                            if (isScanning) {
                                stopScanning();
                            }
                            clearResults(); // Clear previous results
                            updateResult(`Scanning image: ${file.name}...`, 'info');

                            // Scan the selected file
                            html5QrCode.scanFile(file, /* showImage= */ true) // showImage=true renders image in reader div
                                .then(onScanSuccess) // Use the same success handler
                                .catch(onScanFailure); // Use the same failure handler
                        } else {
                            console.log("File selection cancelled or no file chosen.");
                            clearResults(); // Clear results if selection is cancelled
                        }
                        // Reset the input value. This allows selecting the same file again
                        // immediately after a scan attempt (success or failure).
                        e.target.value = null;
                    });
                }

                /**
                 * Clears the content and styling of the results display area.
                 */
                function clearResults() {
                    updateResult('', 'clear');
                }

                // --- Initialization and Event Listeners ---
                 document.addEventListener('DOMContentLoaded', () => {
                     console.log("DOM Content Loaded. Initializing scanner and listeners.");
                     initializeQrCodeScanner(); // Initialize the scanner library instance
                     setupFileInputListener(); // Set up the listener for file uploads

                     // Add click listeners for Start/Stop buttons if they exist
                     if (startBtn) {
                        startBtn.addEventListener('click', startScanning);
                     } else {
                        console.warn("Start Scan button ('start-scan-btn') not found.");
                     }
                     if (stopBtn) {
                        stopBtn.addEventListener('click', stopScanning);
                     } else {
                        console.warn("Stop Scan button ('stop-scan-btn') not found.");
                     }
                 });

                 // Add cleanup logic for when the user navigates away from the page
                 window.addEventListener('beforeunload', () => {
                    if (isScanning && html5QrCode) {
                        console.log("Page unloading. Stopping active scanner.");
                        // Attempt to stop the scanner gracefully. Errors here are usually ignored by the browser.
                        try { html5QrCode.stop(); } catch(e) { console.warn("Ignoring error during scanner stop on page unload:", e); }
                    }
                 });

            </script>
        @endpush
    @endif {{-- End @if(!isAdmin) for script push --}}

</x-app-layout>
