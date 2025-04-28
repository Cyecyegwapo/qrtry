{{-- Use the component layout --}}
<x-app-layout>
    {{-- Define the header content --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('QR Code for Event: ') }} {{ $event->title }}
        </h2>
    </x-slot>

    {{-- Main Content Area --}}
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8"> {{-- Centered content, max-width xl --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100"> {{-- Removed text-center from card body --}}

                    {{-- Page Title --}}
                    <h1 class="text-2xl font-semibold mb-6 text-center text-gray-800 dark:text-gray-200">
                       QR Code: {{ $event->title }}
                    </h1>

                    {{-- QR Code Display Area --}}
                    <div class="text-center my-6"> {{-- Centered this section, added vertical margin --}}
                        @if(!empty($qrCodeSvg))
                            {{-- Display the SVG code --}}
                            <div class="inline-block border border-gray-300 dark:border-gray-600 p-3 bg-white rounded-lg shadow-inner">
                                {!! $qrCodeSvg !!}
                            </div>
                            <p class="mt-4 text-base text-gray-700 dark:text-gray-300">
                                Scan this QR code to record your attendance.
                            </p>

                            {{-- === DOWNLOAD BUTTON (Tailwind Styled) === --}}
                            <div class="mt-4">
                                 <a href="{{ route('events.qrcode.download', $event->id) }}" {{-- Pass event ID or object --}}
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    title="Download QR Code (SVG)">
                                     Download QR Code
                                 </a>
                            </div>
                            {{-- === END DOWNLOAD BUTTON === --}}

                        @else
                            {{-- Error message styled with Tailwind --}}
                            <div class="px-4 py-3 leading-normal text-red-700 bg-red-100 border border-red-300 rounded-lg dark:text-red-200 dark:bg-red-900/50 dark:border-red-800" role="alert">
                                <p>QR Code data is not available for this event.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Event Details Summary (Tailwind Styled) --}}
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                         <h5 class="mb-3 text-lg font-medium text-gray-900 dark:text-gray-100">Event Details Summary:</h5>
                         <dl class="text-sm">
                             <div class="grid grid-cols-3 gap-4 py-1">
                                 <dt class="col-span-1 font-semibold text-gray-600 dark:text-gray-400">Title:</dt>
                                 <dd class="col-span-2 text-gray-800 dark:text-gray-200">{{ $event->title }}</dd>
                             </div>
                              <div class="grid grid-cols-3 gap-4 py-1">
                                 <dt class="col-span-1 font-semibold text-gray-600 dark:text-gray-400">Date:</dt>
                                 <dd class="col-span-2 text-gray-800 dark:text-gray-200">{{ optional($event->date)->format('F j, Y') ?? 'N/A' }}</dd>
                             </div>
                              <div class="grid grid-cols-3 gap-4 py-1">
                                 <dt class="col-span-1 font-semibold text-gray-600 dark:text-gray-400">Time:</dt>
                                 <dd class="col-span-2 text-gray-800 dark:text-gray-200">{{ $event->time ? \Carbon\Carbon::parse($event->time)->format('g:i A') : 'N/A' }}</dd>
                             </div>
                              <div class="grid grid-cols-3 gap-4 py-1">
                                 <dt class="col-span-1 font-semibold text-gray-600 dark:text-gray-400">Location:</dt>
                                 <dd class="col-span-2 text-gray-800 dark:text-gray-200">{{ $event->location ?? 'N/A' }}</dd>
                             </div>
                         </dl>
                    </div>

                    {{-- Back Button (Tailwind Styled) --}}
                    <div class="mt-8 text-center"> {{-- Centered the button --}}
                        <a href="{{ route('events.show', $event->id) }}" {{-- Pass event ID or object --}}
                           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            Back to Event Details
                        </a>
                    </div>

                </div> {{-- End Inner Padding Div --}}
            </div> {{-- End Card Div --}}
        </div> {{-- End Max Width Container --}}
    </div> {{-- End Outer Padding Div --}}

</x-app-layout>