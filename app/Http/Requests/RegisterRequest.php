<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'numeric', 'digits_between:8,15'],
            'birth_date' => ['required', 'date', 'before_or_equal:' . now()->subYears(16)->format('Y-m-d')],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->whereNotNull('email_verified_at');
                }),
            ],
            'password' => ['required', 'min:4'],
            'confirm_password' => ['required', 'same:password'],
        ];
    }

    public function messages()
    {
        return [
            'birth_date.before_or_equal' => 'You must be over 16 years old.',
        ];
    }
}
