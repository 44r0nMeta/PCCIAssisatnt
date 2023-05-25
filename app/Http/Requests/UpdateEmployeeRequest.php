<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'mtle' => ['required', 'string', 'max:80', Rule::unique('employees', 'mtle')->ignore($this->employee)],
            'firstname' => ['required', 'string', 'max:80'],
            'lastname' => ['required', 'string', 'max:80'],
            'bithday' => ['nullable', 'date'],
            'gender' => ['required', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'string', 'email'],
            'address' => ['string', 'nullable', 'max:100'],
            'team' => ['required', 'exists:teams,id'],
            'contract_type' => ['string', 'nullable', 'max:20'],
        ];
    }
}
