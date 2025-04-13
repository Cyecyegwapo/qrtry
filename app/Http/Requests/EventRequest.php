<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only allow users who are logged in AND have the 'admin' role
        // Assumes you have an isAdmin() method on your User model
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define the validation rules for the event form fields
        return [
            'title' => 'required|string|max:255', // Title is required, must be text, max 255 chars
            'description' => 'required|string',     // Description is required, must be text
            'date' => 'required|date|after_or_equal:today', // Date is required, must be a valid date, on or after today
            'time' => 'required|date_format:H:i', // Time is required, must be in HH:MM format (e.g., 14:30)
            'location' => 'required|string|max:255', // Location is required, must be text, max 255 chars
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Optional: Define custom error messages
        return [
            'date.after_or_equal' => 'The event date must be today or a future date.',
            'time.date_format' => 'The time must be in a valid HH:MM format (e.g., 09:00 or 17:30).',
        ];
    }
}
