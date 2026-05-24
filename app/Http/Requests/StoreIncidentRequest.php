<?php

namespace App\Http\Requests;

use App\Enums\SeverityLevel;
use App\Enums\IncidentStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'severity' => ['required', 'in:' . implode(',', SeverityLevel::values())],
            'status' => ['required', 'in:' . implode(',', IncidentStatus::values())],
            'incident_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Incident title is required',
            'title.min' => 'Incident title must be at least 5 characters',
            'title.max' => 'Incident title cannot exceed 255 characters',
            'description.required' => 'Description is required',
            'description.min' => 'Description must be at least 10 characters',
            'description.max' => 'Description cannot exceed 5000 characters',
            'severity.required' => 'Severity level is required',
            'severity.in' => 'Severity must be Low, Medium, High, or Critical',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be Open, On Progress, or Resolved',
            'incident_date.required' => 'Incident date is required',
            'incident_date.date' => 'Incident date must be a valid date',
            'incident_date.before_or_equal' => 'Incident date cannot be in the future',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reported_by' => auth()->id(),
        ]);
    }
}
