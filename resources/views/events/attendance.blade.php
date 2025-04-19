
    {{-- Header slot content --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- Display the title of the event for which attendance is being shown --}}
            Attendance List for: {{ $event->title }}
        </h2>
    </x-slot>

    {{-- Main content area --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Display the total count of attendees --}}
                    <p class="mb-4"><strong>Total Attendees:</strong> {{ $attendees->count() }}</p>

                    {{-- Table to display attendees --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    {{-- Table header for Attendee Name --}}
                                    <th scope="col" class="py-3 px-6">
                                        Name
                                    </th>
                                    {{-- Table header for Attendee Email --}}
                                    <th scope="col" class="py-3 px-6">
                                        Email
                                    </th>
                                    {{-- Table header for Attendance Timestamp --}}
                                    <th scope="col" class="py-3 px-6">
                                        Attended At
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through each attendee passed from the controller --}}
                                @forelse($attendees as $attendee)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        {{-- Attendee Name column --}}
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $attendee->name }}
                                        </td>
                                        {{-- Attendee Email column --}}
                                        <td class="py-4 px-6">
                                            {{ $attendee->email }}
                                        </td>
                                        {{-- Attendance Timestamp column --}}
                                        <td class="py-4 px-6">
                                            {{-- Format the timestamp if it exists, otherwise display N/A --}}
                                            {{ $attendee->attended_at ? \Carbon\Carbon::parse($attendee->attended_at)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Message shown if there are no attendees --}}
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="3" class="py-4 px-6 text-center text-gray-500 dark:text-gray-400">
                                            No attendees recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                     {{-- Back Button --}}
                     <div class="mt-6">
                        {{-- Link back to the specific event's detail page --}}
                        <a href="{{ route('events.show', $event->id) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">
                            &larr; Back to Event Details
                        </a>
                     </div>

                </div>
            </div>
        </div>
    </div>

