{{-- Use the component layout ONLY --}}
<x-app-layout>
    {{-- Define the header content for this specific page --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Events List') }}
        </h2>
    </x-slot>

    {{-- Main Content Area --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Display success message (Tailwind Styled) --}}
            @if(session('success'))
                <div class="px-4 py-3 mb-6 leading-normal text-green-700 bg-green-100 border border-green-300 rounded-lg dark:text-green-200 dark:bg-green-900/50 dark:border-green-800" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

             {{-- Top Bar with Title and Create Button --}}
             <div class="flex justify-between items-center mb-6">
                 <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Events</h1>
                 {{-- "Create Event" button only shown once, for admins --}}
                 @if(auth()->user()->isAdmin())
                     <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                         Create Event
                     </a>
                 @endif
            </div>


            {{-- Card container for the table --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                {{-- Responsive Table Container --}}
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                        {{-- Table Header: Emerald/Stone Theme --}}
                        <thead class="text-xs text-emerald-800 uppercase bg-emerald-100 dark:bg-emerald-900/50 dark:text-emerald-200">
                            <tr>
                                <th scope="col" class="py-3 px-6">Title</th>
                                <th scope="col" class="py-3 px-6">Date</th>
                                <th scope="col" class="py-3 px-6">Time</th>
                                <th scope="col" class="py-3 px-6">Location</th>
                                @if(auth()->user()->isAdmin()) {{-- QR Status column only for Admins --}}
                                    <th scope="col" class="py-3 px-6">QR Status</th> {{-- QR Status Header --}}
                                @endif
                                <th scope="col" class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event) {{-- Use forelse for empty check --}}
                                {{-- Table Rows: Stone Theme Hover/Border --}}
                                <tr class="bg-white border-b border-stone-200 dark:bg-gray-800 dark:border-stone-700 hover:bg-stone-50 dark:hover:bg-stone-700/50 align-middle">
                                    {{-- Title --}}
                                    <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $event->title }}</td>
                                    {{-- Date --}}
                                    <td class="py-4 px-6 whitespace-nowrap">{{ optional($event->date)->format('M j, Y') }}</td>
                                    {{-- Time --}}
                                    <td class="py-4 px-6 whitespace-nowrap">{{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}</td>
                                    {{-- Location --}}
                                    <td class="py-4 px-6">{{ $event->location }}</td>

                                    {{-- QR Status Column Data (Admin only) --}}
                                    @if(auth()->user()->isAdmin())
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            @if ($event->qrcode)
                                                {{-- Tailwind Badge --}}
                                                <span class="px-2 py-0.5 text-xs rounded-full {{ $event->qrcode->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                    {{ $event->qrcode->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                {{-- Uncomment to show current validity text --}}
                                                {{--
                                                <span class="block mt-1 text-xs {{ $event->qrcode->isValidNow() ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    ({{ $event->qrcode->isValidNow() ? 'Valid Now' : 'Invalid Now' }})
                                                </span>
                                                --}}
                                            @else
                                                {{-- Tailwind Badge for N/A --}}
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-stone-100 text-stone-800 dark:bg-stone-700 dark:text-stone-300">
                                                    N/A
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                    {{-- End QR Status Column Data --}}

                                    {{-- Actions Column --}}
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex justify-end items-center space-x-2">
                                            {{-- View Button (Always visible) --}}
                                            <a href="{{ route('events.show', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="View Details">View</a>

                                            {{-- Admin Only Actions --}}
                                            @if(auth()->user()->isAdmin())
                                                {{-- Edit Button --}}
                                                <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-stone-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-stone-600 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="Edit Event & QR Settings">Edit</a>
                                                {{-- QR Page Button --}}
                                                <a href="{{ route('events.qrcode', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="View QR Code Page">QR</a>
                                                {{-- Attendance Button --}}
                                                <a href="{{ route('events.attendance', $event->id) }}" class="inline-flex items-center px-3 py-1.5 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="View Attendance List">List</a>
                                                {{-- Delete Form/Button --}}
                                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="Delete Event">Del</button> {{-- Shortened Text --}}
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- Adjust colspan based on whether QR Status column is visible --}}
                                    <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" class="text-center py-6 px-6 text-gray-500 dark:text-gray-400 italic">
                                        No events found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div> {{-- End Overflow Container --}}
            </div> {{-- End Card --}}
        </div> {{-- End Max Width Container --}}
    </div> {{-- End Outer Padding Div --}}

</x-app-layout>