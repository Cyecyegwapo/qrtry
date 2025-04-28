{{-- Use the component layout --}}
<x-app-layout>
    {{-- Define the header for the edit page --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Event: ') }} {{ $event->title }}
        </h2>
    </x-slot>

    {{-- Main Content Area --}}
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6"> {{-- Max width 4xl, space between cards --}}

            {{-- Session Success Messages (Tailwind Styled) --}}
            @if (session('success'))
                <div class="px-4 py-3 leading-normal text-green-700 bg-green-100 border border-green-300 rounded-lg dark:text-green-200 dark:bg-green-900/50 dark:border-green-800" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

             {{-- Validation Errors List (Tailwind Styled) --}}
             @if ($errors->any())
                <div class="px-4 py-3 mb-4 leading-normal text-red-700 bg-red-100 border border-red-300 rounded-lg dark:text-red-200 dark:bg-red-900/50 dark:border-red-800" role="alert">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- ==          CARD 1: EDIT EVENT DETAILS                == --}}
            {{-- ======================================================== --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                 <div class="p-6 sm:p-8 border-b border-gray-200 dark:border-gray-700">
                     <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Update Event Details</h3>
                     <form action="{{ route('events.update', $event->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Title Field --}}
                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('title', 'updateEvent') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('title', $event->title) }}" required>
                            @error('title', 'updateEvent') {{-- Use error bag if needed --}}
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description Field --}}
                        <div>
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" id="description" rows="4"
                                      class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('description', 'updateEvent') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                      >{{ old('description', $event->description) }}</textarea>
                            @error('description', 'updateEvent')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date Field --}}
                        <div>
                            <label for="date" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="date" id="date"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('date', 'updateEvent') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('date', optional($event->date)->format('Y-m-d')) }}" required>
                            @error('date', 'updateEvent')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                         {{-- Time Field --}}
                        <div>
                            <label for="time" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Time <span class="text-red-500">*</span></label>
                            <input type="time" name="time" id="time"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('time', 'updateEvent') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('time', $event->time ? \Carbon\Carbon::parse($event->time)->format('H:i') : '') }}" required>
                            @error('time', 'updateEvent')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Location Field --}}
                        <div>
                            <label for="location" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Location <span class="text-red-500">*</span></label>
                            <input type="text" name="location" id="location"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('location', 'updateEvent') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('location', $event->location) }}" required>
                            @error('location', 'updateEvent')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Year Level Field --}}
                        <div>
                            <label for="year_level" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Target Year Level (Optional)</label>
                            <input type="text" name="year_level" id="year_level"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('year_level', 'updateEvent') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('year_level', $event->year_level) }}">
                            @error('year_level', 'updateEvent')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Department Field --}}
                        <div>
                            <label for="department" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Target Department (Optional)</label>
                            <input type="text" name="department" id="department"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('department', 'updateEvent') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('department', $event->department) }}">
                            @error('department', 'updateEvent')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button for Event Details --}}
                        <div class="flex items-center justify-end pt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Update Event Details
                            </button>
                        </div>
                    </form>
                 </div>
            </div> {{-- End Card 1 --}}


            {{-- ======================================================== --}}
            {{-- ==          CARD 2: EDIT QR CODE SETTINGS             == --}}
            {{-- ======================================================== --}}
             <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-gray-200 dark:border-gray-700">
                     <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Update QR Code Settings</h3>

                     {{-- Check if QR Code exists before showing form --}}
                     {{-- Controller needs to pass $event with qrcode loaded: $event->load('qrcode') --}}
                     @if ($event->qrcode)
                        <form method="POST" action="{{ route('events.updateQrSettings', $event->id) }}" class="space-y-6">
                            @csrf
                            @method('PUT') {{-- Method spoofing for PUT --}}

                            {{-- Active From Field --}}
                            <div>
                                <label for="qr_active_from" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">QR Active From (Optional)</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Leave blank to make active immediately.</p>
                                <input type="datetime-local" id="qr_active_from" name="active_from"
                                       class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('active_from', 'updateQrSettings') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                       value="{{ old('active_from', optional($event->qrcode->active_from)->format('Y-m-d\TH:i')) }}">
                                @error('active_from', 'updateQrSettings') {{-- Check specific error bag --}}
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Active Until Field --}}
                            <div>
                                <label for="qr_active_until" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">QR Active Until (Optional)</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Leave blank for no expiration date.</p>
                                <input type="datetime-local" id="qr_active_until" name="active_until"
                                       class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('active_until', 'updateQrSettings') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                       value="{{ old('active_until', optional($event->qrcode->active_until)->format('Y-m-d\TH:i')) }}">
                                @error('active_until', 'updateQrSettings')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Is Active Checkbox --}}
                            <div class="block pt-2"> {{-- Add padding top --}}
                                <label for="qr_is_active" class="flex items-center">
                                    {{-- Hidden input for false value --}}
                                    <input type="hidden" name="is_active" value="0">
                                    <input id="qr_is_active" name="is_active" type="checkbox" value="1"
                                           class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 @error('is_active', 'updateQrSettings') border-red-500 dark:border-red-600 ring-1 ring-red-500 @enderror"
                                           {{-- Use ?? true for default if creating/null --}}
                                           {{ old('is_active', $event->qrcode->is_active ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 font-medium">QR Code Enabled</span>
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 ml-6">Uncheck to force-disable scanning, regardless of dates.</p>
                                @error('is_active', 'updateQrSettings')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Submit Button for QR Settings --}}
                            <div class="flex items-center justify-end pt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-stone-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-stone-700 focus:bg-stone-700 active:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Update QR Settings
                                </button>
                            </div>
                        </form>
                     @else
                         {{-- Message if no QR code exists --}}
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">QR Code record not found. A basic QR code will be generated automatically if needed, or when settings are first saved.</p>
                     @endif
                </div>
            </div> {{-- End Card 2 --}}


             {{-- Cancel Button Area --}}
            <div class="mt-6 flex justify-start">
                {{-- Go back to the Show page --}}
                <a href="{{ route('events.show', $event->id) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel / Back to Event
                </a>
            </div>


        </div> {{-- End Max Width Container --}}
    </div> {{-- End Outer Padding Div --}}

</x-app-layout>