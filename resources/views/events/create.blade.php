{{-- Use the component layout ONLY --}}
<x-app-layout>
    {{-- Define the header content for this specific page --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Event') }}
        </h2>
    </x-slot>

    {{-- Main Content Area --}}
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8"> {{-- Centered content, max-width 3xl for form --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 border-b border-gray-200 dark:border-gray-700">

                     {{-- Display validation errors list if any (Tailwind Styled) --}}
                     @if ($errors->any())
                        <div class="mb-6 px-4 py-3 leading-normal text-red-700 bg-red-100 border border-red-300 rounded-lg dark:text-red-200 dark:bg-red-900/50 dark:border-red-800" role="alert">
                            <p class="font-bold">Please fix the following errors:</p>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Create Event Form with Tailwind Styling --}}
                    <form action="{{ route('events.store') }}" method="POST" class="space-y-6">
                        @csrf {{-- CSRF Protection --}}

                        {{-- Title Field --}}
                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('title') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('title') }}" required>
                            {{-- Display individual validation error --}}
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description Field --}}
                        <div>
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" id="description" rows="4"
                                      class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('description') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                      >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date Field --}}
                        <div>
                            <label for="date" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="date" id="date"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('date') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('date') }}" required>
                            @error('date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                         {{-- Time Field --}}
                        <div>
                            <label for="time" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Time <span class="text-red-500">*</span></label>
                            <input type="time" name="time" id="time"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('time') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('time') }}" required>
                            @error('time')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Location Field --}}
                        <div>
                            <label for="location" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Location <span class="text-red-500">*</span></label>
                            <input type="text" name="location" id="location"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('location') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('location') }}" required>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Year Level Field (Optional) --}}
                        <div>
                            <label for="year_level" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Target Year Level (Optional)</label>
                            <input type="text" name="year_level" id="year_level"
                                   placeholder="e.g., 1st Year, All Levels"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('year_level') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                   value="{{ old('year_level') }}">
                            @error('year_level')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Department Field (Optional) --}}
                        <div>
                            <label for="department" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Target Department (Optional)</label>
                             <input type="text" name="department" id="department"
                                    placeholder="e.g., CCS, Engineering, All Departments"
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm @error('department') border-red-500 dark:border-red-600 focus:border-red-600 dark:focus:border-red-500 focus:ring-red-600 dark:focus:ring-red-500 @enderror"
                                    value="{{ old('department') }}">
                            @error('department')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- +++ NEW FEATURE: Enable QR Code Checkbox +++ --}}
                        <div class="block pt-2"> {{-- Added padding-top for spacing --}}
                            <label for="enable_qr_code" class="inline-flex items-center cursor-pointer">
                                <input id="enable_qr_code" type="checkbox"
                                       class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                       name="enable_qr_code" value="1" {{ old('enable_qr_code') ? 'checked' : '' }}> {{-- Maintain state on validation failure --}}
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Enable QR Code scanning immediately') }}</span>
                            </label>
                            {{-- Display potential validation error (optional for checkbox, but good practice) --}}
                            @error('enable_qr_code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            {{-- Helper text for clarity --}}
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">If unchecked, the QR code will be generated but disabled. You can enable it later from the event details page.</p>
                        </div>
                        {{-- +++ END NEW FEATURE +++ --}}


                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end pt-4 space-x-4 border-t border-gray-200 dark:border-gray-700 mt-6"> {{-- Added border-top and margin-top --}}
                            {{-- Cancel Button --}}
                             <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            {{-- Create Button --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Create Event
                            </button>
                        </div>

                    </form> {{-- End Form --}}

                </div> {{-- End Inner Padding Div --}}
            </div> {{-- End Card Div --}}
        </div> {{-- End Max Width Container --}}
    </div> {{-- End Outer Padding Div --}}

</x-app-layout>
