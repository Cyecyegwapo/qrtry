{{-- Assuming this view uses your main app layout --}}
<x-app-layout>
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

    {{-- 2. Include the html5-qrcode library --}}
    {{-- Put this ideally before your closing </body> tag or in a scripts section --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    {{-- 3. JavaScript to Initialize and Control the Scanner --}}
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
            if (error instanceof Error && document.getElementById('qr-input-file').files.length > 0) {
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
            if (!isScanning || !html5QrCode || !html5QrCode.isScanning) return; // Check if library thinks it's scanning

            html5QrCode.stop().then((ignore) => {
                console.log("QR Code scanning stopped.");
                isScanning = false;
                startBtn.style.display = 'inline-flex';
                stopBtn.style.display = 'none';
                document.getElementById('qr-reader').innerHTML = ''; // Clear video feed area
            }).catch((err) => {
                console.error("Failed to stop scanner:", err);
                isScanning = false; // Assume stopped even on error
                startBtn.style.display = 'inline-flex';
                stopBtn.style.display = 'none';
            });
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

        // --- File Upload Scanning Logic --- ADDED ---
        const fileInput = document.getElementById('qr-input-file');

        fileInput.addEventListener('change', e => {
            if (e.target.files && e.target.files.length > 0) {
                const file = e.target.files[0];
                clearResults();
                resultsElement.textContent = 'Scanning uploaded file...';

                // Use the same html5QrCode instance to scan the file
                // The second argument 'true' means show the image in the qr-reader div (optional)
                html5QrCode.scanFile(file, true)
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
            if (isScanning) {
                stopScanning();
            }
        });

    </script>

</x-app-layout>
