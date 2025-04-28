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
                                                 <span class="px-2 py-0.5 text-xs rounded-full {{ $event->qrcode->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
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
                                                  <span class="px-2 py-0.5 text-xs rounded-full {{ $event->qrcode->isValidNow() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                      {{ $event->qrcode->isValidNow() ? 'Yes' : 'No' }}
                                                  </span>
                                                  {{-- Reason if not valid --}}
                                                  @if(!$event->qrcode->isValidNow())
                                                     <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                         (Reason:
                                                         @if(!$event->qrcode->is_active) Admin Disabled
                                                         @elseif($event->qrcode->active_from && now()->lt($event->qrcode->active_from)) Not started
                                                         @elseif($event->qrcode->active_until && now()->gt($event->qrcode->active_until)) Expired
                                                         @else Unknown @endif)
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
        @push('scripts')
            <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
            <script>
                // Pass PHP variables safely to JS
                const currentPageEventId = @json($event->id);
                const appBaseUrl = @json(rtrim(url('/'), '/')); // Get base URL and remove trailing slash
                console.log('Base URL for Check:', appBaseUrl); // Verify this in console

                // --- Common Variables and Functions ---
                const resultsElement = document.getElementById('qr-reader-results');
                const qrReaderDiv = document.getElementById('qr-reader');
                const fileInput = document.getElementById('qr-input-file');
                const startBtn = document.getElementById('start-scan-btn');
                const stopBtn = document.getElementById('stop-scan-btn');
                let html5QrCode = null;

                function initializeQrCodeScanner() {
                    if (!html5QrCode && document.getElementById("qr-reader")) {
                        try {
                            html5QrCode = new Html5Qrcode("qr-reader");
                        } catch (e) { console.error("Init Fail:", e); if(startBtn) startBtn.disabled = true; if(fileInput) fileInput.disabled = true; if(qrReaderDiv) qrReaderDiv.innerHTML = '<p class="text-red-500 p-4">Scanner Fail</p>';}
                    } else if (!document.getElementById("qr-reader")) {
                         console.error("QR Reader element not found");
                         if(startBtn) startBtn.disabled = true; if(fileInput) fileInput.disabled = true;
                    }
                }

                function updateResult(message, type = 'info') {
                     if (!resultsElement) return;
                     resultsElement.textContent = message;
                     resultsElement.className = 'mt-6 text-center font-medium py-3 px-4 rounded-lg min-h-[50px] text-sm '; // Base
                     // Clear old colors
                    resultsElement.classList.remove(...['bg-green-100/50', 'dark:bg-green-900/50', 'text-green-700', 'dark:text-green-200', 'bg-red-100/50', 'dark:bg-red-900/50', 'text-red-700', 'dark:text-red-200', 'bg-yellow-100/50', 'dark:bg-yellow-900/50', 'text-yellow-700', 'dark:text-yellow-200', 'bg-blue-100/50', 'dark:bg-blue-900/50', 'text-blue-700', 'dark:text-blue-200', 'bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300']);
                     // Add new colors
                     switch (type) {
                         case 'success': resultsElement.classList.add('bg-green-100/50', 'dark:bg-green-900/50', 'text-green-700', 'dark:text-green-200'); break;
                         case 'error': resultsElement.classList.add('bg-red-100/50', 'dark:bg-red-900/50', 'text-red-700', 'dark:text-red-200'); break;
                         case 'warning': resultsElement.classList.add('bg-yellow-100/50', 'dark:bg-yellow-900/50', 'text-yellow-700', 'dark:text-yellow-200'); break;
                         case 'info': resultsElement.classList.add('bg-blue-100/50', 'dark:bg-blue-900/50', 'text-blue-700', 'dark:text-blue-200'); break;
                         case 'clear': resultsElement.textContent = ''; break;
                         default: resultsElement.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300'); break;
                     }
                }

                // --- onScanSuccess with Stricter Validation (Using Regex Literal + startsWith) ---
                 function onScanSuccess(decodedText, decodedResult) {
                    console.log(`Scan Success: Code = ${decodedText}`, decodedResult);
                    stopScanning(); // Stop camera if running

                    // 1. Define the simple path pattern using a Regex Literal
                    const pathPattern = /\/events\/(\d+)\/attendance\/record$/; // Checks the end part of the URL

                    // 2. Test the path pattern first
                    const match = decodedText.match(pathPattern);

                    // 3. Check BOTH path pattern match AND if it starts with the correct base URL
                    // Ensure appBaseUrl is defined and not empty before checking startsWith
                    if (match && match[1] && appBaseUrl && decodedText.startsWith(appBaseUrl)) {
                        // Pattern matched AND it's from our app URL

                        const scannedEventId = parseInt(match[1], 10);
                        console.log("Scanned Event ID:", scannedEventId);

                        // 4. Check if the scanned Event ID matches the current page's Event ID
                        if (scannedEventId === currentPageEventId) {
                            updateResult('Valid attendance code found! Redirecting...', 'success');
                            // Redirect to the scanned URL (which is validated)
                            setTimeout(() => { window.location.href = decodedText; }, 500);
                        } else {
                            // Correct format & origin, but WRONG event ID
                            updateResult(`Invalid QR: Code is for a different event.`, 'error');
                            console.log(`Event ID mismatch: Scanned ${scannedEventId}, Expected ${currentPageEventId}`);
                        }
                    } else {
                        // Either path didn't match OR it didn't start with appBaseUrl
                        updateResult(`Invalid QR: Code does not match expected format or origin.`, 'error');
                        if (!match) {
                            console.log("Failed Path Pattern Match:", decodedText);
                        } else if (!appBaseUrl || !decodedText.startsWith(appBaseUrl)) {
                            console.log("Failed Origin Check:", decodedText, "Expected origin starting with:", appBaseUrl);
                        }
                    }
                }
                 // --- End of onScanSuccess ---

                function onScanFailure(error) {
                     if (error && fileInput && fileInput.value) {
                         let errorMessage = "Could not scan from file.";
                         if (error instanceof Error) { errorMessage = `Error scanning file: ${error.message || error}`; }
                         else if (typeof error === 'string') { errorMessage = error.toLowerCase().includes('no qr code found') ? `No QR code found.` : `Error: ${error}`; }
                         updateResult(errorMessage, 'error');
                         fileInput.value = '';
                     }
                     // Avoid logging continuous "No QR code found" errors from camera unless debugging
                     if (!(typeof error === 'string' && error.includes('No QR code found'))) { console.warn(`Scan Fail: ${error}`); }
                }

                // --- Live Camera Logic ---
                let isScanning = false;
                function startScanning() {
                    if (isScanning || !html5QrCode) return;
                    clearResults();
                    qrReaderDiv.innerHTML = ''; // Clear placeholder
                    updateResult('Initializing camera...', 'info');
                    qrReaderDiv.classList.remove('items-center', 'justify-center'); // Allow video placement
                    Html5Qrcode.getCameras().then(devices => {
                        if (devices && devices.length) {
                           // Try to find back camera first, fallback to first camera
                           const camId = devices.find(d=>d.label.toLowerCase().includes('back'))?.id || devices[0].id;
                           console.log(`Using camera: ${camId}`);
                           // Function to calculate qrbox size dynamically
                           const qrboxFunction = (viewfinderWidth, viewfinderHeight) => {
                                let minEdgePercentage = 0.7; // Use 70% of the smaller edge
                                let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                                let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                                return { width: qrboxSize, height: qrboxSize };
                            };
                           const config = { fps: 10, qrbox: qrboxFunction, rememberLastUsedCamera: false, supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA] };
                           html5QrCode.start(camId, config, onScanSuccess, onScanFailure)
                            .then(() => {
                                console.log("Scanner started successfully.");
                                isScanning = true;
                                if(startBtn) startBtn.style.display = 'none';
                                if(stopBtn) stopBtn.style.display = 'inline-flex';
                                updateResult('Scanning... Point camera at QR code.', 'info');
                             })
                            .catch(handleCameraError);
                        } else { handleCameraError('No cameras found.'); }
                    }).catch(handleCameraError);
                }
                function stopScanning() {
                     if (!html5QrCode || !html5QrCode.isScanning) { // Check if scanning before trying to stop
                        console.log("Stop requested but scanner not active.");
                        isScanning = false; // Ensure state is correct
                        if(startBtn) startBtn.style.display = 'inline-flex';
                        if(stopBtn) stopBtn.style.display = 'none';
                        // Ensure placeholder is shown if div is empty
                        if (qrReaderDiv && !qrReaderDiv.querySelector('video') && !qrReaderDiv.querySelector('canvas')) {
                            qrReaderDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-500 text-sm p-4">Camera view stopped.</p>';
                            qrReaderDiv.classList.add('items-center', 'justify-center');
                        }
                        return;
                    }
                     // Attempt to stop, handle potential errors, always update UI state
                     html5QrCode.stop().then(()=> console.log("Scanner stopped.")).catch(err => console.error("Stop fail:", err))
                     .finally(() => {
                         isScanning = false;
                         if(startBtn) startBtn.style.display = 'inline-flex';
                         if(stopBtn) stopBtn.style.display = 'none';
                         if(qrReaderDiv) {
                             qrReaderDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-500 text-sm p-4">Camera view stopped.</p>'; // Reset placeholder
                             qrReaderDiv.classList.add('items-center', 'justify-center');
                         }
                         updateResult('Scanner stopped.', 'info');
                     });
                }
                 function handleCameraError(err) {
                     console.error("Cam Error:", err); let msg = 'Camera error. ';
                     if (err instanceof Error) { msg += err.message; } else if(typeof err === 'string'){ msg += err; }
                     updateResult(msg, 'error');
                     isScanning = false; // Reset state
                     if(startBtn) startBtn.style.display = 'inline-flex';
                     if(stopBtn) stopBtn.style.display = 'none';
                     if(qrReaderDiv) { // Show error in reader div
                         qrReaderDiv.innerHTML = `<p class="text-red-500 dark:text-red-300 p-4 text-sm font-medium">Camera Error</p>`;
                         qrReaderDiv.classList.add('items-center', 'justify-center');
                     }
                 }

                // --- File Upload Logic ---
                if (fileInput) {
                     fileInput.addEventListener('change', e => {
                         if (!html5QrCode) initializeQrCodeScanner(); if (!html5QrCode) return;
                         if (e.target.files?.length) {
                             if (isScanning) { stopScanning(); } // Stop camera if scanning
                             clearResults();
                             updateResult('Scanning file...', 'info');
                             html5QrCode.scanFile(e.target.files[0], true) // true = show image during scan
                                 .then(onScanSuccess)
                                 .catch(onScanFailure); // Use updated failure handler
                         } else { clearResults(); }
                     });
                } else { console.warn("File input element not found."); }

                // --- Utility ---
                function clearResults() { updateResult('', 'clear'); }

                // --- Init and Listeners ---
                 document.addEventListener('DOMContentLoaded', () => {
                     initializeQrCodeScanner(); // Try to initialize on load
                     // Add listeners only if buttons exist
                     if(startBtn) startBtn.addEventListener('click', startScanning);
                     if(stopBtn) stopBtn.addEventListener('click', stopScanning);
                 });
                 // Clean up scanner when leaving the page
                 window.addEventListener('beforeunload', () => { if (isScanning && html5QrCode) stopScanning(); });

            </script>
        @endpush
    @endif {{-- End @if(!isAdmin) for script push --}}

</x-app-layout>