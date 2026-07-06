<?php

declare(strict_types=1);

namespace App\Http\Requests\Employee;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'company_id' => ['required', 'exists:companies,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'user_id' => ['nullable', 'exists:users,id',
                Rule::unique('employees', 'user_id')->ignore($employeeId),
            ],
            'employee_code' => ['nullable', 'string', 'max:255',
                Rule::unique('employees', 'employee_code')->ignore($employeeId),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'hire_date' => ['required', 'date'],
            'termination_date' => ['nullable', 'date', 'after_or_equal:hire_date'],
            'status' => ['required', 'string', 'in:active,inactive,suspended,terminated'],
            'employment_type' => ['required', 'string', 'in:full_time,part_time,contract,intern'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already taken.',
            'email.email' => 'Please provide a valid email address.',
            'hire_date.required' => 'Hire date is required.',
            'status.in' => 'Status must be active, inactive, suspended, or terminated.',
            'employment_type.in' => 'Employment type must be full_time, part_time, contract, or intern.',
            'salary.min' => 'Salary must be at least 0.',
            'termination_date.after_or_equal' => 'Termination date must be after or equal to hire date.',
            'user_id.unique' => 'This user is already linked to another employee.',
            'employee_code.unique' => 'This employee code is already taken.',
        ];
    }
}
