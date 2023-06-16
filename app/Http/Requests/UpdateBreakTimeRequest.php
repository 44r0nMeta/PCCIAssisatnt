<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBreakTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string'],
            'day' => ['nullable', 'date'],
            'employee_id' => ['required', 'exists:employees,id'],
            'expected_start_time' => ['required'],
            'expected_end_time' => ['required', 'after_or_equal:' . $this->expected_start_time],
            'started_time' => ['nullable'],
            'ended_time' => ['nullable'],
            'status' => ['nullable'],
            'memo' => ['nullable', 'max:255']
        ];
    }
}
