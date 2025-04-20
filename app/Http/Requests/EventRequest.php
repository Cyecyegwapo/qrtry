<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set authorization logic if needed (e.g., check if user is admin)
        return true; // Or use Auth::check() or specific permission check
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i', // Or H:i:s if seconds needed
            'location' => 'required|string|max:255',
            'year_level' => 'nullable|string|max:100', // Added rule (adjust max length)
            'department' => 'nullable|string|max:100', // Added rule (adjust max length)
        ];
    }
}