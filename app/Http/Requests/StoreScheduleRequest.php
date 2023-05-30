<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
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
            'type' => ['required'],
            'day' => ['required', 'date', 'after_or_equal:today'],
            'employee_id' => ['required', 'exists:employees,id'],
            'expected_start_time' => ['required'],
            'expected_end_time' => ['required', 'after_or_equal:' . $this->expected_start_time],
            'started_time' => ['nullable'],
            'ended_time' => ['nullable', 'before_or_equal:' . $this->started_time],
            'breakdown_total_min' => ['nullable', 'integer'],
            'memo' => ['nullable', 'max:255'],
            'status' => ['nullable']
        ];
    }
}
