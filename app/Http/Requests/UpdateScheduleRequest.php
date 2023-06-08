<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            // Rule::unique('exams')->where(function ($query) use ($this) {
            //     return $query->where('exam_name', $this->exam_name)
            //         ->where('exam_year', $this->exam_year)
            //         ->where('student_id', $this->student_id);
            // }),
            'type' => ['required'],
            'day' => ['required', 'date'],
            'employee_id' => ['required', 'exists:employees,id'],
            'expected_start_time' => ['required'],
            'expected_end_time' => ['required', 'after_or_equal:' . $this->expected_start_time],
            'started_time' => ['nullable'],
            'ended_time' => ['nullable'],
            'breakdown_total_min' => ['nullable', 'integer'],
            'status' => ['nullable'],
            'memo' => ['nullable', 'max:255']
        ];
    }
}
