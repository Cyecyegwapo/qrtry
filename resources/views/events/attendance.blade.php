<x-app-layout> 

    {{-- Header slot content --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- Display the title of the event --}}
            Attendance List for: <span class="italic">{{ $event->title }}</span>
        </h2>
    </x-slot>

    {{-- Main content area --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Card container --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Top section with title and count --}}
                    <div class="mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100">
                            Attendee Records
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Total Attendees: <span class="font-bold">{{ $attendees->count() }}</span>
                        </p>
                    </div>

                    {{-- Table to display attendees --}}
                    <div class="overflow-x-auto relative border border-gray-200 dark:border-gray-700 sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                            {{-- Table Header --}}
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">
                                        Name
                                    </th>
                                    <th scope="col" class="py-3 px-6">
                                        Email
                                    </th>
                                    <th scope="col" class="py-3 px-6">
                                        Attended At (Timestamp)
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through attendees --}}
                                @forelse($attendees as $attendee)
                                    {{-- Added alternating row colors for better readability --}}
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600/50 odd:bg-white dark:odd:bg-gray-800 even:bg-gray-50 dark:even:bg-gray-700/50">
                                        {{-- Attendee Name --}}
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $attendee->name }}
                                        </td>
                                        {{-- Attendee Email --}}
                                        <td class="py-4 px-6">
                                            {{ $attendee->email }}
                                        </td>
                                        {{-- Attendance Timestamp --}}
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            {{-- More readable date/time format --}}
                                            {{ $attendee->attended_at ? \Carbon\Carbon::parse($attendee->attended_at)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Message if no attendees --}}
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="3" class="py-6 px-6 text-center text-gray-500 dark:text-gray-400 italic">
                                            No attendees recorded yet for this event.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                     {{-- Back Button (Styled Consistently) --}}
                     <div class="mt-6 flex justify-start"> {{-- Aligned button left --}}
                        <a href="{{ route('events.show', $event->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            &larr; Back to Event Details
                        </a>
                     </div>

                </div> {{-- End p-6 --}}
            </div> {{-- End card --}}
        </div> {{-- End max-w-7xl --}}
    </div> {{-- End py-12 --}}
</x-app-layout> 